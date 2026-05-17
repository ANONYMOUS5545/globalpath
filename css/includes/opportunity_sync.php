<?php
require_once __DIR__ . '/config.php';

function getOpportunitySyncSettings() {
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $settings = [
        'refresh_interval_seconds' => 1800,
        'http_timeout_seconds' => 8,
        'max_jobs_per_source' => 30,
        'max_scholarships_per_source' => 24,
        'job_sources' => [
            [
                'key' => 'greenhouse_stripe',
                'label' => 'Stripe Official Careers',
                'driver' => 'greenhouse',
                'board_token' => 'stripe',
                'organization' => 'Stripe',
                'sector' => 'Fintech',
                'official_url_substrings' => ['https://stripe.com/jobs/'],
            ],
            [
                'key' => 'greenhouse_cloudflare',
                'label' => 'Cloudflare Official Careers',
                'driver' => 'greenhouse',
                'board_token' => 'cloudflare',
                'organization' => 'Cloudflare',
                'sector' => 'Technology',
                'official_url_substrings' => ['https://boards.greenhouse.io/cloudflare/'],
            ],
            [
                'key' => 'greenhouse_datadog',
                'label' => 'Datadog Official Careers',
                'driver' => 'greenhouse',
                'board_token' => 'datadog',
                'organization' => 'Datadog',
                'sector' => 'Technology',
                'official_url_substrings' => ['https://careers.datadoghq.com/', 'https://boards.greenhouse.io/datadog/'],
            ],
        ],
        'scholarship_sources' => [
            [
                'key' => 'scholars4dev_feed',
                'label' => 'Scholars4Dev',
                'driver' => 'rss',
                'url' => 'https://www.scholars4dev.com/feed/',
            ],
            [
                'key' => 'opportunitydesk_scholarships',
                'label' => 'Opportunity Desk',
                'driver' => 'rss',
                'url' => 'https://opportunitydesk.org/tag/scholarships-2/feed/',
            ],
        ],
    ];

    return $settings;
}

function bootOpportunitySync(PDO $db, $type, array $options = []) {
    $fallback = [
        'sync_key' => opportunitySyncKey($type),
        'status' => 'idle',
        'last_success_at' => null,
        'last_completed_at' => null,
        'last_item_count' => 0,
        'last_error' => null,
    ];

    try {
        ensureOpportunitySyncSchema($db);
        maybeRefreshOpportunityFeed($db, $type, $options);
        return getOpportunitySyncState($db, $type);
    } catch (Throwable $e) {
        $fallback['status'] = 'error';
        $fallback['last_error'] = $e->getMessage();
        return $fallback;
    }
}

function ensureOpportunitySyncSchema(PDO $db) {
    static $ready = false;
    if ($ready) {
        return;
    }

    $db->exec("
        CREATE TABLE IF NOT EXISTS external_sync_state (
            sync_key VARCHAR(120) PRIMARY KEY,
            content_type VARCHAR(30) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'idle',
            last_started_at DATETIME NULL,
            last_completed_at DATETIME NULL,
            last_success_at DATETIME NULL,
            last_item_count INT NOT NULL DEFAULT 0,
            last_error TEXT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    ensureOpportunityTableColumns($db, 'jobs');
    ensureOpportunityTableColumns($db, 'scholarships');

    $ready = true;
}

function ensureOpportunityTableColumns(PDO $db, $table) {
    $columns = [
        'listing_origin' => "ALTER TABLE {$table} ADD COLUMN listing_origin VARCHAR(20) NOT NULL DEFAULT 'manual' AFTER created_by",
        'external_source_key' => "ALTER TABLE {$table} ADD COLUMN external_source_key VARCHAR(120) NULL AFTER listing_origin",
        'external_id' => "ALTER TABLE {$table} ADD COLUMN external_id VARCHAR(191) NULL AFTER external_source_key",
        'published_at' => "ALTER TABLE {$table} ADD COLUMN published_at DATETIME NULL AFTER external_id",
        'last_seen_at' => "ALTER TABLE {$table} ADD COLUMN last_seen_at DATETIME NULL AFTER published_at",
        'last_synced_at' => "ALTER TABLE {$table} ADD COLUMN last_synced_at DATETIME NULL AFTER last_seen_at",
        'live_metadata' => "ALTER TABLE {$table} ADD COLUMN live_metadata MEDIUMTEXT NULL AFTER last_synced_at",
    ];

    foreach ($columns as $column => $sql) {
        if (!opportunityColumnExists($db, $table, $column)) {
            $db->exec($sql);
        }
    }

    if (!opportunityIndexExists($db, $table, "ux_{$table}_external")) {
        $db->exec("ALTER TABLE {$table} ADD UNIQUE KEY ux_{$table}_external (external_source_key, external_id)");
    }

    if (!opportunityIndexExists($db, $table, "idx_{$table}_origin_active")) {
        $db->exec("ALTER TABLE {$table} ADD INDEX idx_{$table}_origin_active (listing_origin, is_active)");
    }
}

function opportunityColumnExists(PDO $db, $table, $column) {
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function opportunityIndexExists(PDO $db, $table, $indexName) {
    $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND INDEX_NAME = ?
    ");
    $stmt->execute([$table, $indexName]);
    return (int)$stmt->fetchColumn() > 0;
}

function maybeRefreshOpportunityFeed(PDO $db, $type, array $options = []) {
    $state = getOpportunitySyncState($db, $type);
    $settings = getOpportunitySyncSettings();
    $refreshInterval = isset($options['refresh_interval_seconds'])
        ? max(60, (int)$options['refresh_interval_seconds'])
        : $settings['refresh_interval_seconds'];
    $force = !empty($options['force']);

    if (!$force) {
        $lastAttemptAt = $state['last_success_at'] ?? null;
        if (empty($lastAttemptAt) && !empty($state['last_completed_at'])) {
            $lastAttemptAt = $state['last_completed_at'];
        }

        $lastAttempt = $lastAttemptAt ? strtotime($lastAttemptAt) : false;
        if ($lastAttempt && (time() - $lastAttempt) < $refreshInterval) {
            return;
        }
    }

    $lockName = 'globalpath_live_sync_' . $type;
    $lockStmt = $db->prepare("SELECT GET_LOCK(?, 0)");
    $lockStmt->execute([$lockName]);
    $hasLock = (int)$lockStmt->fetchColumn() === 1;
    if (!$hasLock) {
        return;
    }

    try {
        updateOpportunitySyncState($db, $type, [
            'status' => 'running',
            'last_started_at' => date('Y-m-d H:i:s'),
            'last_error' => null,
        ]);

        $result = ($type === 'job')
            ? syncImportedJobs($db)
            : syncImportedScholarships($db);

        $status = !empty($result['errors']) && !empty($result['success_count']) ? 'partial' : 'success';
        if (!empty($result['errors']) && empty($result['success_count'])) {
            $status = 'error';
        }

        $payload = [
            'status' => $status,
            'last_completed_at' => date('Y-m-d H:i:s'),
            'last_item_count' => (int)($result['item_count'] ?? 0),
            'last_error' => !empty($result['errors']) ? implode(' | ', $result['errors']) : null,
        ];

        if (!empty($result['success_count'])) {
            $payload['last_success_at'] = date('Y-m-d H:i:s');
        }

        updateOpportunitySyncState($db, $type, $payload);
    } catch (Throwable $e) {
        updateOpportunitySyncState($db, $type, [
            'status' => 'error',
            'last_completed_at' => date('Y-m-d H:i:s'),
            'last_error' => $e->getMessage(),
        ]);
    } finally {
        $releaseStmt = $db->prepare("SELECT RELEASE_LOCK(?)");
        $releaseStmt->execute([$lockName]);
    }
}

function syncImportedJobs(PDO $db) {
    $settings = getOpportunitySyncSettings();
    $requests = [];

    foreach ($settings['job_sources'] as $source) {
        $requests[$source['key']] = [
            'url' => 'https://boards-api.greenhouse.io/v1/boards/' . rawurlencode($source['board_token']) . '/jobs?content=true',
            'timeout' => $settings['http_timeout_seconds'],
        ];
    }

    $responses = fetchHttpResponses($requests);
    $errors = [];
    $successCount = 0;
    $totalItems = 0;

    foreach ($settings['job_sources'] as $source) {
        $response = $responses[$source['key']] ?? null;
        if (!$response || empty($response['ok'])) {
            $errors[] = $source['label'] . ': ' . ($response['error'] ?? 'Unable to reach source');
            continue;
        }

        try {
            $items = parseGreenhouseJobs($response['body'], $source);
            if (count($items) > $settings['max_jobs_per_source']) {
                $items = array_slice($items, 0, $settings['max_jobs_per_source']);
            }

            $seenIds = [];
            foreach ($items as $item) {
                upsertImportedJob($db, $item);
                $seenIds[] = $item['external_id'];
            }

            deactivateMissingImportedRecords($db, 'jobs', $source['key'], $seenIds);
            $successCount++;
            $totalItems += count($items);
        } catch (Throwable $e) {
            $errors[] = $source['label'] . ': ' . $e->getMessage();
        }
    }

    deactivateImportedRecordsFromUnknownSources(
        $db,
        'jobs',
        array_map(static function ($source) {
            return $source['key'];
        }, $settings['job_sources'])
    );

    markExpiredImportedRecordsInactive($db, 'jobs');

    return [
        'success_count' => $successCount,
        'item_count' => $totalItems,
        'errors' => $errors,
    ];
}

function syncImportedScholarships(PDO $db) {
    $settings = getOpportunitySyncSettings();
    $requests = [];

    foreach ($settings['scholarship_sources'] as $source) {
        $requests[$source['key']] = [
            'url' => $source['url'],
            'timeout' => $settings['http_timeout_seconds'],
        ];
    }

    $responses = fetchHttpResponses($requests);
    $errors = [];
    $successCount = 0;
    $totalItems = 0;

    foreach ($settings['scholarship_sources'] as $source) {
        $response = $responses[$source['key']] ?? null;
        if (!$response || empty($response['ok'])) {
            $errors[] = $source['label'] . ': ' . ($response['error'] ?? 'Unable to reach source');
            continue;
        }

        try {
            $items = parseScholarshipFeed($response['body'], $source);
            if (count($items) > $settings['max_scholarships_per_source']) {
                $items = array_slice($items, 0, $settings['max_scholarships_per_source']);
            }

            $seenIds = [];
            foreach ($items as $item) {
                upsertImportedScholarship($db, $item);
                $seenIds[] = $item['external_id'];
            }

            deactivateMissingImportedRecords($db, 'scholarships', $source['key'], $seenIds);
            $successCount++;
            $totalItems += count($items);
        } catch (Throwable $e) {
            $errors[] = $source['label'] . ': ' . $e->getMessage();
        }
    }

    deactivateImportedRecordsFromUnknownSources(
        $db,
        'scholarships',
        array_map(static function ($source) {
            return $source['key'];
        }, $settings['scholarship_sources'])
    );

    markExpiredImportedRecordsInactive($db, 'scholarships');

    return [
        'success_count' => $successCount,
        'item_count' => $totalItems,
        'errors' => $errors,
    ];
}

function fetchHttpResponses(array $requests) {
    if (empty($requests)) {
        return [];
    }

    if (function_exists('curl_multi_init') && function_exists('curl_init')) {
        return fetchHttpResponsesWithCurl($requests);
    }

    return fetchHttpResponsesSequentially($requests);
}

function fetchHttpResponsesWithCurl(array $requests) {
    $multi = curl_multi_init();
    $handles = [];
    $results = [];

    foreach ($requests as $key => $request) {
        $ch = curl_init($request['url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => (int)($request['timeout'] ?? 8),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'GlobalPathAfricaLiveSync/1.0 (+https://globalpathafrica.org)',
            CURLOPT_HTTPHEADER => ['Accept: application/json, application/rss+xml, application/xml, text/xml;q=0.9, */*;q=0.8'],
        ]);
        curl_multi_add_handle($multi, $ch);
        $handles[$key] = $ch;
    }

    $active = null;
    do {
        $status = curl_multi_exec($multi, $active);
        if ($active) {
            curl_multi_select($multi, 1.0);
        }
    } while ($active && $status === CURLM_OK);

    foreach ($handles as $key => $ch) {
        $body = curl_multi_getcontent($ch);
        $error = curl_error($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $ok = $error === '' && $statusCode >= 200 && $statusCode < 400 && $body !== false && $body !== '';

        $results[$key] = [
            'ok' => $ok,
            'status' => $statusCode,
            'body' => is_string($body) ? $body : '',
            'error' => $ok ? null : ($error !== '' ? $error : 'HTTP ' . $statusCode),
        ];

        curl_multi_remove_handle($multi, $ch);
        curl_close($ch);
    }

    curl_multi_close($multi);
    return $results;
}

function fetchHttpResponsesSequentially(array $requests) {
    $results = [];

    foreach ($requests as $key => $request) {
        $timeout = (int)($request['timeout'] ?? 8);
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeout,
                'header' => "User-Agent: GlobalPathAfricaLiveSync/1.0\r\nAccept: application/json, application/rss+xml, application/xml, text/xml;q=0.9, */*;q=0.8\r\n",
            ],
        ]);

        $body = @file_get_contents($request['url'], false, $context);
        $statusCode = 0;
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $match)) {
            $statusCode = (int)$match[1];
        }

        $results[$key] = [
            'ok' => $body !== false && $statusCode >= 200 && $statusCode < 400,
            'status' => $statusCode,
            'body' => is_string($body) ? $body : '',
            'error' => $body !== false ? null : ('HTTP ' . $statusCode),
        ];
    }

    return $results;
}

function parseGreenhouseJobs($body, array $source) {
    $payload = json_decode($body, true);
    if (!is_array($payload) || empty($payload['jobs']) || !is_array($payload['jobs'])) {
        throw new RuntimeException('Unexpected job feed format');
    }

    $items = [];
    foreach ($payload['jobs'] as $job) {
        $title = cleanImportedText($job['title'] ?? '');
        $link = trim((string)($job['absolute_url'] ?? ''));
        if ($title === '' || $link === '') {
            continue;
        }

        if (!isAcceptedImportedJobLink($link, $source)) {
            continue;
        }

        $content = normalizeImportedHtmlText($job['content'] ?? '');
        $location = cleanImportedText($job['location']['name'] ?? 'Remote / Multiple Locations');
        $publishedAt = parseFlexibleDateTime($job['updated_at'] ?? null);
        $requirements = extractNamedSection($content, ['requirements', 'qualifications', 'what you will need', 'who you are']);
        $salary = extractSalaryFromText($content);

        $items[] = [
            'title' => $title,
            'organization' => $source['organization'],
            'location' => $location ?: 'Remote / Multiple Locations',
            'country' => inferCountryFromText($location) ?: 'Global / Remote',
            'description' => truncateImportedText($content ?: ($source['organization'] . ' is hiring. Open the official posting for full details.'), 6000),
            'requirements' => $requirements,
            'salary_range' => $salary,
            'deadline' => extractFutureDateFromText($content),
            'link' => $link,
            'source_org' => $source['label'],
            'job_type' => inferJobType($title . "\n" . $content),
            'sector' => inferJobSector($title . "\n" . $content, $source['sector'] ?? ''),
            'published_at' => $publishedAt,
            'external_source_key' => $source['key'],
            'external_id' => (string)($job['id'] ?? sha1($link)),
            'live_metadata' => json_encode([
                'driver' => 'greenhouse',
                'board_token' => $source['board_token'],
                'location' => $location,
                'updated_at' => $job['updated_at'] ?? null,
            ], JSON_UNESCAPED_SLASHES),
        ];
    }

    usort($items, function ($a, $b) {
        return strcmp($b['published_at'] ?? '', $a['published_at'] ?? '');
    });

    return $items;
}

function isAcceptedImportedJobLink($link, array $source) {
    $allowedSubstrings = $source['official_url_substrings'] ?? [];
    if (empty($allowedSubstrings)) {
        return true;
    }

    foreach ($allowedSubstrings as $allowedSubstring) {
        if (stripos($link, $allowedSubstring) === 0) {
            return true;
        }
    }

    return false;
}

function parseScholarshipFeed($body, array $source) {
    $xml = @simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);
    if (!$xml) {
        throw new RuntimeException('Invalid XML feed');
    }

    $items = [];
    foreach (extractFeedEntries($xml) as $entry) {
        $title = cleanImportedText($entry['title'] ?? '');
        $link = trim((string)($entry['link'] ?? ''));
        if ($title === '' || $link === '') {
            continue;
        }

        if (!shouldImportScholarshipEntry($title, $entry['description'] ?? '')) {
            continue;
        }

        $description = normalizeImportedHtmlText($entry['description'] ?? '');
        $contentBlob = $title . "\n" . $description;

        $items[] = [
            'title' => $title,
            'provider' => inferScholarshipProvider($title, $source['label']),
            'country' => inferCountryFromText($contentBlob) ?: 'Global',
            'description' => truncateImportedText($description ?: 'Open the source listing for the latest scholarship details and application steps.', 6000),
            'eligibility' => extractNamedSection($description, ['eligibility', 'requirements', 'who can apply']),
            'benefits' => extractNamedSection($description, ['benefits', 'funding', 'scholarship value', 'value']),
            'deadline' => extractFutureDateFromText($contentBlob),
            'link' => $link,
            'source_org' => $source['label'],
            'field_of_study' => inferScholarshipFieldOfStudy($contentBlob),
            'level' => inferScholarshipLevel($contentBlob),
            'type' => inferScholarshipType($contentBlob),
            'published_at' => parseFlexibleDateTime($entry['published_at'] ?? null),
            'external_source_key' => $source['key'],
            'external_id' => sha1($link),
            'live_metadata' => json_encode([
                'driver' => 'rss',
                'source_url' => $source['url'] ?? null,
                'published_at' => $entry['published_at'] ?? null,
            ], JSON_UNESCAPED_SLASHES),
        ];
    }

    usort($items, function ($a, $b) {
        return strcmp($b['published_at'] ?? '', $a['published_at'] ?? '');
    });

    return $items;
}

function extractFeedEntries(SimpleXMLElement $xml) {
    $entries = [];

    if (isset($xml->channel->item)) {
        foreach ($xml->channel->item as $item) {
            $contentNs = $item->children('content', true);
            $entries[] = [
                'title' => (string)$item->title,
                'link' => (string)$item->link,
                'description' => (string)($contentNs->encoded ?: $item->description),
                'published_at' => (string)$item->pubDate,
            ];
        }
        return $entries;
    }

    if (isset($xml->entry)) {
        foreach ($xml->entry as $entry) {
            $link = '';
            if (isset($entry->link)) {
                foreach ($entry->link as $linkNode) {
                    $attrs = $linkNode->attributes();
                    if (!empty($attrs['href'])) {
                        $link = (string)$attrs['href'];
                        break;
                    }
                }
            }

            $entries[] = [
                'title' => (string)$entry->title,
                'link' => $link,
                'description' => (string)($entry->summary ?: $entry->content),
                'published_at' => (string)($entry->updated ?: $entry->published),
            ];
        }
    }

    return $entries;
}

function upsertImportedJob(PDO $db, array $item) {
    $sql = "
        INSERT INTO jobs (
            title, organization, location, country, description, requirements, salary_range, deadline, link, source_org,
            job_type, sector, is_premium_only, is_featured, is_active, created_by, listing_origin, external_source_key,
            external_id, published_at, last_seen_at, last_synced_at, live_metadata
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, 0, 0, 1, NULL, 'imported', ?,
            ?, ?, NOW(), NOW(), ?
        )
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            organization = VALUES(organization),
            location = VALUES(location),
            country = VALUES(country),
            description = VALUES(description),
            requirements = VALUES(requirements),
            salary_range = VALUES(salary_range),
            deadline = VALUES(deadline),
            link = VALUES(link),
            source_org = VALUES(source_org),
            job_type = VALUES(job_type),
            sector = VALUES(sector),
            is_active = 1,
            published_at = VALUES(published_at),
            last_seen_at = NOW(),
            last_synced_at = NOW(),
            live_metadata = VALUES(live_metadata)
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        $item['title'],
        $item['organization'],
        $item['location'],
        $item['country'],
        $item['description'],
        $item['requirements'],
        $item['salary_range'],
        $item['deadline'],
        $item['link'],
        $item['source_org'],
        $item['job_type'],
        $item['sector'],
        $item['external_source_key'],
        $item['external_id'],
        $item['published_at'],
        $item['live_metadata'],
    ]);
}

function upsertImportedScholarship(PDO $db, array $item) {
    $sql = "
        INSERT INTO scholarships (
            title, provider, country, description, eligibility, benefits, deadline, link, source_org, field_of_study,
            level, type, image, is_featured, is_active, african_countries, created_by, listing_origin, external_source_key,
            external_id, published_at, last_seen_at, last_synced_at, live_metadata
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, NULL, 0, 1, NULL, NULL, 'imported', ?,
            ?, ?, NOW(), NOW(), ?
        )
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            provider = VALUES(provider),
            country = VALUES(country),
            description = VALUES(description),
            eligibility = VALUES(eligibility),
            benefits = VALUES(benefits),
            deadline = VALUES(deadline),
            link = VALUES(link),
            source_org = VALUES(source_org),
            field_of_study = VALUES(field_of_study),
            level = VALUES(level),
            type = VALUES(type),
            is_active = 1,
            published_at = VALUES(published_at),
            last_seen_at = NOW(),
            last_synced_at = NOW(),
            live_metadata = VALUES(live_metadata)
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        $item['title'],
        $item['provider'],
        $item['country'],
        $item['description'],
        $item['eligibility'],
        $item['benefits'],
        $item['deadline'],
        $item['link'],
        $item['source_org'],
        $item['field_of_study'],
        $item['level'],
        $item['type'],
        $item['external_source_key'],
        $item['external_id'],
        $item['published_at'],
        $item['live_metadata'],
    ]);
}

function deactivateMissingImportedRecords(PDO $db, $table, $sourceKey, array $seenIds) {
    if (empty($seenIds)) {
        $stmt = $db->prepare("
            UPDATE {$table}
            SET is_active = 0, last_synced_at = NOW()
            WHERE listing_origin = 'imported'
              AND external_source_key = ?
        ");
        $stmt->execute([$sourceKey]);
        return;
    }

    $placeholders = implode(',', array_fill(0, count($seenIds), '?'));
    $params = array_merge([$sourceKey], $seenIds);
    $stmt = $db->prepare("
        UPDATE {$table}
        SET is_active = 0, last_synced_at = NOW()
        WHERE listing_origin = 'imported'
          AND external_source_key = ?
          AND external_id NOT IN ({$placeholders})
    ");
    $stmt->execute($params);
}

function deactivateImportedRecordsFromUnknownSources(PDO $db, $table, array $activeSourceKeys) {
    $activeSourceKeys = array_values(array_filter(array_unique($activeSourceKeys)));
    if (empty($activeSourceKeys)) {
        $db->exec("
            UPDATE {$table}
            SET is_active = 0
            WHERE listing_origin = 'imported'
        ");
        return;
    }

    $placeholders = implode(',', array_fill(0, count($activeSourceKeys), '?'));
    $stmt = $db->prepare("
        UPDATE {$table}
        SET is_active = 0
        WHERE listing_origin = 'imported'
          AND external_source_key NOT IN ({$placeholders})
    ");
    $stmt->execute($activeSourceKeys);
}

function markExpiredImportedRecordsInactive(PDO $db, $table) {
    $db->exec("
        UPDATE {$table}
        SET is_active = 0
        WHERE listing_origin = 'imported'
          AND deadline IS NOT NULL
          AND deadline < CURDATE()
    ");
}

function getOpportunitySyncState(PDO $db, $type) {
    $stmt = $db->prepare("SELECT * FROM external_sync_state WHERE sync_key = ?");
    $stmt->execute([opportunitySyncKey($type)]);
    $state = $stmt->fetch();

    if (!$state) {
        return [
            'sync_key' => opportunitySyncKey($type),
            'content_type' => $type,
            'status' => 'idle',
            'last_started_at' => null,
            'last_completed_at' => null,
            'last_success_at' => null,
            'last_item_count' => 0,
            'last_error' => null,
        ];
    }

    return $state;
}

function updateOpportunitySyncState(PDO $db, $type, array $fields) {
    $current = getOpportunitySyncState($db, $type);
    $data = array_merge($current, $fields, [
        'sync_key' => opportunitySyncKey($type),
        'content_type' => $type,
    ]);

    $stmt = $db->prepare("
        INSERT INTO external_sync_state (
            sync_key, content_type, status, last_started_at, last_completed_at, last_success_at, last_item_count, last_error
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
        ON DUPLICATE KEY UPDATE
            content_type = VALUES(content_type),
            status = VALUES(status),
            last_started_at = VALUES(last_started_at),
            last_completed_at = VALUES(last_completed_at),
            last_success_at = VALUES(last_success_at),
            last_item_count = VALUES(last_item_count),
            last_error = VALUES(last_error)
    ");

    $stmt->execute([
        $data['sync_key'],
        $data['content_type'],
        $data['status'] ?? 'idle',
        $data['last_started_at'] ?? null,
        $data['last_completed_at'] ?? null,
        $data['last_success_at'] ?? null,
        (int)($data['last_item_count'] ?? 0),
        $data['last_error'] ?? null,
    ]);
}

function opportunitySyncKey($type) {
    return ($type === 'job' ? 'jobs' : 'scholarships') . '_live_sync';
}

function normalizeImportedHtmlText($html) {
    $html = html_entity_decode((string)$html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = preg_replace('/<(br|\\/p|\\/div|\\/li|\\/h[1-6])\\b[^>]*>/i', "\n", $html);
    $html = preg_replace('/<li\\b[^>]*>/i', "- ", $html);
    $html = strip_tags($html);
    $html = str_replace(["\r\n", "\r"], "\n", $html);
    $html = preg_replace("/\n{3,}/", "\n\n", $html);
    return trim(cleanImportedText($html));
}

function cleanImportedText($text) {
    $text = (string)$text;
    $text = preg_replace('/[^\\P{C}\n\t]+/u', ' ', $text);
    $text = preg_replace("/\r\n|\r/u", "\n", $text);
    $text = preg_replace('/[ \t]+/u', ' ', $text);
    $text = preg_replace("/\n{3,}/u", "\n\n", $text);
    return trim($text);
}

function truncateImportedText($text, $limit = 3000) {
    $text = trim((string)$text);
    if (strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(substr($text, 0, $limit - 3)) . '...';
}

function inferJobType($blob) {
    $blob = strtolower((string)$blob);
    if (preg_match('/\\bintern(ship)?\\b/', $blob)) {
        return 'internship';
    }
    if (preg_match('/\\bpart[- ]?time\\b/', $blob)) {
        return 'part_time';
    }
    if (preg_match('/\\bcontract\\b|\\bfixed[- ]term\\b/', $blob)) {
        return 'contract';
    }
    if (preg_match('/\\bvolunteer\\b/', $blob)) {
        return 'volunteer';
    }
    return 'full_time';
}

function inferJobSector($blob, $fallback = '') {
    $blob = strtolower((string)$blob);
    $map = [
        'Healthcare' => ['health', 'medical', 'clinical', 'hospital', 'public health'],
        'Education' => ['education', 'learning', 'curriculum', 'teacher', 'student'],
        'Research & Economics' => ['research', 'economics', 'analyst', 'science', 'scientist'],
        'Environment & Climate' => ['climate', 'environment', 'sustainability', 'carbon'],
        'NGO & Non-Profit' => ['nonprofit', 'non-profit', 'ngo', 'foundation', 'humanitarian'],
        'Technology' => ['software', 'engineer', 'developer', 'data', 'product', 'security', 'cloud', 'ai'],
        'Finance' => ['finance', 'payments', 'accounting', 'risk', 'banking', 'fintech'],
    ];

    foreach ($map as $sector => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($blob, $keyword) !== false) {
                return $sector;
            }
        }
    }

    return $fallback ?: 'Technology';
}

function inferScholarshipProvider($title, $fallback) {
    $patterns = [
        '/(University of [A-Z][A-Za-z&,\-\'\s]+)/',
        '/([A-Z][A-Za-z&,\-\'\s]+ University)/',
        '/([A-Z][A-Za-z&,\-\'\s]+ Scholarships?)/',
        '/([A-Z][A-Za-z&,\-\'\s]+ Fellowship)/',
        '/([A-Z][A-Za-z&,\-\'\s]+ Government)/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $title, $match)) {
            return trim($match[1]);
        }
    }

    $parts = preg_split('/[:|-]/', $title);
    if (!empty($parts[0]) && stripos($parts[0], 'scholarship') === false && strlen(trim($parts[0])) > 3) {
        return trim($parts[0]);
    }

    return $fallback;
}

function inferScholarshipLevel($blob) {
    $blob = strtolower((string)$blob);
    $hasUndergraduate = preg_match('/\bundergraduate\b|\bbachelor/', $blob);
    $hasPostgraduate = preg_match('/\bpostgraduate\b|\bmasters?\b|\bgraduate\b/', $blob);
    $hasPhd = preg_match('/\bphd\b|\bdoctoral\b|\bdoctorate\b/', $blob);

    $count = (int)$hasUndergraduate + (int)$hasPostgraduate + (int)$hasPhd;
    if ($count > 1) {
        return 'all';
    }
    if ($hasPhd) {
        return 'phd';
    }
    if ($hasPostgraduate) {
        return 'postgraduate';
    }
    if ($hasUndergraduate) {
        return 'undergraduate';
    }
    return 'all';
}

function inferScholarshipType($blob) {
    $blob = strtolower((string)$blob);
    if (preg_match('/\bfellowship\b/', $blob)) {
        return 'fellowship';
    }
    if (preg_match('/\bexchange\b/', $blob)) {
        return 'exchange';
    }
    if (preg_match('/fully funded|full scholarship|covers tuition and living|tuition, living expenses/i', $blob)) {
        return 'full';
    }
    return 'partial';
}

function inferScholarshipFieldOfStudy($blob) {
    $blob = strtolower((string)$blob);
    $map = [
        'STEM' => ['stem', 'engineering', 'computer science', 'technology', 'science', 'mathematics'],
        'Business & Economics' => ['business', 'finance', 'economics', 'accounting', 'management'],
        'Health Sciences' => ['health', 'medicine', 'medical', 'nursing', 'public health'],
        'Social Sciences' => ['development', 'policy', 'social science', 'international relations'],
        'Arts & Humanities' => ['arts', 'humanities', 'history', 'literature', 'language'],
    ];

    foreach ($map as $field => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($blob, $keyword) !== false) {
                return $field;
            }
        }
    }

    return 'Multiple Fields';
}

function inferCountryFromText($text) {
    $text = strtolower((string)$text);
    $map = [
        'Germany' => ['germany', 'german'],
        'United Kingdom' => ['united kingdom', 'uk', 'britain', 'england', 'scotland', 'wales'],
        'United States' => ['united states', 'usa', 'u.s.', 'us-'],
        'Canada' => ['canada'],
        'Australia' => ['australia'],
        'Netherlands' => ['netherlands', 'dutch'],
        'France' => ['france', 'french'],
        'Belgium' => ['belgium'],
        'Italy' => ['italy'],
        'Sweden' => ['sweden'],
        'Finland' => ['finland'],
        'New Zealand' => ['new zealand'],
        'Global' => ['remote', 'worldwide', 'global', 'multiple locations'],
        'Kenya' => ['kenya', 'nairobi'],
        'Nigeria' => ['nigeria', 'lagos'],
        'South Africa' => ['south africa'],
        'Ghana' => ['ghana'],
        'Rwanda' => ['rwanda'],
        'Uganda' => ['uganda'],
    ];

    foreach ($map as $country => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return $country;
            }
        }
    }

    return null;
}

function shouldImportScholarshipEntry($title, $description) {
    $blob = strtolower($title . ' ' . strip_tags((string)$description));
    $positive = preg_match('/\bscholarship\b|\bscholarships\b|\bfellowship\b|\bfellowships\b|\bstudentship\b|\bbursary\b|\btuition waiver\b/', $blob);
    if (!$positive) {
        return false;
    }

    return !looksLikeScholarshipRoundup($title);
}

function looksLikeScholarshipRoundup($title) {
    $title = strtolower((string)$title);
    $patterns = [
        '/\bcurrently open\b/',
        '/\bglobal opportunities\b/',
        '/\bdeadline fast approaching\b/',
        '/\blist of\b/',
        '/\btop \d+/',
        '/^\d+\+?\s/',
        '/\broundup\b/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $title)) {
            return true;
        }
    }

    return false;
}

function extractNamedSection($text, array $headings) {
    $text = trim((string)$text);
    if ($text === '') {
        return null;
    }

    foreach ($headings as $heading) {
        $pattern = '/(?:^|\n)' . preg_quote($heading, '/') . '\s*[:\-]?\s*(.+?)(?=\n[A-Z][A-Za-z ]{2,25}\s*[:\-]|\z)/is';
        if (preg_match($pattern, $text, $match)) {
            return truncateImportedText(cleanImportedText($match[1]), 1500);
        }
    }

    return null;
}

function extractSalaryFromText($text) {
    $text = (string)$text;
    if (preg_match('/((?:USD|EUR|GBP|KES|KSh|\\$)\\s?\d[\d,]*(?:\s?(?:-|to)\s?(?:USD|EUR|GBP|KES|KSh|\\$)?\s?\d[\d,]*)?)/i', $text, $match)) {
        return cleanImportedText($match[1]);
    }
    return null;
}

function extractFutureDateFromText($text) {
    $text = (string)$text;
    $patterns = [
        '/(?:deadline|apply by|closing date|applications close(?: on)?)\s*[:\-]?\s*([A-Z][a-z]+ \d{1,2}, \d{4})/i',
        '/(?:deadline|apply by|closing date|applications close(?: on)?)\s*[:\-]?\s*(\d{1,2} [A-Z][a-z]+ \d{4})/i',
        '/([A-Z][a-z]+ \d{1,2}, \d{4})/i',
        '/(\d{1,2} [A-Z][a-z]+ \d{4})/i',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $match)) {
            $timestamp = strtotime($match[1]);
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        }
    }

    return null;
}

function parseFlexibleDateTime($value) {
    if (!$value) {
        return null;
    }

    $timestamp = strtotime((string)$value);
    if (!$timestamp) {
        return null;
    }

    return date('Y-m-d H:i:s', $timestamp);
}
