<?php
require_once __DIR__ . '/config.php';

function bootSiteContent(PDO $db) {
    static $booted = false;

    if ($booted) {
        return;
    }

    ensureJobListingSchema($db);
    ensureBlogSchema($db);
    ensureJobResourceSchema($db);
    seedDefaultBlogPosts($db);
    seedDefaultJobResources($db);

    $booted = true;
}

function ensureJobListingSchema(PDO $db) {
    if (!contentColumnExists($db, 'jobs', 'access_tier')) {
        $db->exec("ALTER TABLE jobs ADD COLUMN access_tier VARCHAR(20) NOT NULL DEFAULT 'free' AFTER sector");
    }

    $db->exec("
        UPDATE jobs
        SET access_tier = 'premium'
        WHERE is_premium_only = 1
          AND (access_tier IS NULL OR access_tier = '' OR access_tier = 'free')
    ");
}

function contentColumnExists(PDO $db, $table, $column) {
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

function ensureBlogSchema(PDO $db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS blog_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            excerpt TEXT NOT NULL,
            content LONGTEXT NOT NULL,
            category VARCHAR(80) NOT NULL DEFAULT 'guides',
            author_name VARCHAR(120) NOT NULL DEFAULT 'Global Path Africa',
            cover_icon VARCHAR(80) NOT NULL DEFAULT 'fas fa-newspaper',
            reading_time_minutes INT NOT NULL DEFAULT 5,
            is_featured TINYINT(1) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            published_at DATETIME NULL,
            created_by INT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_blog_posts_status (is_active, published_at),
            CONSTRAINT fk_blog_posts_admin FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function ensureJobResourceSchema(PDO $db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS job_resources (
            id INT AUTO_INCREMENT PRIMARY KEY,
            resource_key VARCHAR(120) NOT NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            organization VARCHAR(180) NOT NULL,
            category VARCHAR(80) NOT NULL DEFAULT 'general_jobs',
            region VARCHAR(120) NOT NULL DEFAULT 'Global',
            country VARCHAR(120) NULL,
            resource_type VARCHAR(80) NOT NULL DEFAULT 'official_employer',
            summary TEXT NOT NULL,
            apply_url VARCHAR(500) NOT NULL,
            application_cost_type VARCHAR(30) NOT NULL DEFAULT 'free',
            cost_notes TEXT NULL,
            is_featured TINYINT(1) NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_by INT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_job_resources_status (is_active, category, application_cost_type, sort_order),
            CONSTRAINT fk_job_resources_admin FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function seedDefaultBlogPosts(PDO $db) {
    $existing = (int)$db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    if ($existing > 0) {
        return;
    }

    $posts = [
        [
            'title' => 'How to Apply for Sea Jobs Without Paying Recruiters',
            'slug' => 'how-to-apply-for-sea-jobs-without-paying-recruiters',
            'excerpt' => 'A practical route for applying to cruise and seafarer roles directly through official employer portals like MSC, Royal Caribbean and Maersk.',
            'content' => "Direct sea-job applications should start with the employer's own careers page, not an intermediary promising shortcuts.\n\nStart by targeting official portals that publish shipboard roles directly. MSC Cruises, Royal Caribbean Group and Maersk all maintain their own careers sites where candidates can search openings, create profiles and upload documents.\n\nA safe workflow looks like this:\n1. Confirm the domain belongs to the employer.\n2. Create a candidate profile on the official portal.\n3. Match your CV to the department you want: deck, engine, hospitality, marine operations or guest services.\n4. Prepare the documents the employer will expect later such as passport, sea service records, certificates and medical history.\n5. Ignore anyone asking for interview money, placement fees or training deposits.\n\nPaid items can still appear later in a real sea-job journey, but they are usually not recruitment fees. Typical costs may include visas, medical exams, seaman books or mandatory certifications depending on the role and the flag state. That is very different from paying somebody to 'unlock' the job itself.\n\nWhen in doubt, apply on the employer site first and treat every off-platform payment request as a red flag until the employer confirms it through an official channel.",
            'category' => 'sea_jobs',
            'author_name' => 'Global Path Africa Team',
            'cover_icon' => 'fas fa-anchor',
            'reading_time_minutes' => 6,
            'is_featured' => 1,
            'published_at' => '2026-04-20 09:00:00',
        ],
        [
            'title' => 'Caregiver and Healthcare Roles in the Middle East: Where to Apply Directly',
            'slug' => 'caregiver-and-healthcare-roles-in-the-middle-east-where-to-apply-directly',
            'excerpt' => 'A shortlist of credible hospital and healthcare employer portals in the UAE and Gulf region, with notes on free applications and licensing costs.',
            'content' => "If you are applying for caregiver, nursing, allied health or patient-support roles in the Middle East, begin with large hospital groups and public health systems that run their own hiring channels.\n\nGood examples include Mediclinic Middle East, Cleveland Clinic Abu Dhabi, Burjeel Holdings, Aster DM Healthcare and Emirates Health Services. These employers publish vacancies themselves and usually explain whether you should apply through a portal, by email or through a regulated in-country process.\n\nUse this checklist before you apply:\n1. Read the role title carefully and confirm whether the job is caregiver-facing, nursing, homecare, hospital support or allied health.\n2. Check the licensing requirement listed in the vacancy. Some employers ask for DHA, DOH, MOH or equivalent eligibility.\n3. Apply only through the employer's official careers page or the public-sector recruitment channel linked by that employer.\n4. Save screenshots or confirmation emails after every submission.\n\nMost serious Middle East healthcare employers do not charge an application fee. The paid part usually appears in licensing, document verification, exams, medicals or visa processing. That means the job application can be free while the compliance path around the job still costs money.\n\nThe safest rule is simple: pay only for official regulatory steps you can trace back to a government or employer process, never for access to the vacancy itself.",
            'category' => 'caregiver_jobs',
            'author_name' => 'Global Path Africa Team',
            'cover_icon' => 'fas fa-user-nurse',
            'reading_time_minutes' => 5,
            'is_featured' => 1,
            'published_at' => '2026-04-18 09:00:00',
        ],
        [
            'title' => 'Free vs Paid Job Applications: What You Should Actually Pay For',
            'slug' => 'free-vs-paid-job-applications-what-you-should-actually-pay-for',
            'excerpt' => 'A simple framework for telling the difference between a legitimate free application, an official paid licensing step and a scam.',
            'content' => "Many applicants mix up three very different things: a free application, a paid regulatory requirement and a fake recruitment fee.\n\nA free application means you can submit your profile through the employer or government portal without paying to unlock the job. This is the normal pattern for reputable first-tier employers.\n\nA paid regulatory requirement is different. Some industries require official licensing, exams, document verification, medical checks or visa processing. These costs can be legitimate when they come from a regulator, embassy, testing body or a clearly documented employer process.\n\nA scam fee usually sounds like one of these:\n- pay to be shortlisted\n- pay to reserve your interview slot\n- pay an agent before we send your contract\n- pay for training before the employer has verified you\n\nUse this test before sending money:\n1. Is the fee shown on the employer or regulator's own website?\n2. Does the payment happen inside an official portal or after a written employer instruction you can verify?\n3. Would the process still make sense if the agent disappeared tomorrow?\n\nIf the answer is no, stop the application and verify the step on the official site. A real opportunity should survive independent verification.",
            'category' => 'application_tips',
            'author_name' => 'Global Path Africa Team',
            'cover_icon' => 'fas fa-shield-alt',
            'reading_time_minutes' => 4,
            'is_featured' => 0,
            'published_at' => '2026-04-15 09:00:00',
        ],
        [
            'title' => 'Build a Strong International Caregiver Application Pack',
            'slug' => 'build-a-strong-international-caregiver-application-pack',
            'excerpt' => 'What to prepare before applying for overseas caregiver, nursing support and allied health roles so you can move faster when a good opening appears.',
            'content' => "A strong caregiver application pack makes direct employer portals much easier to use because you are ready to submit the same day you find the right role.\n\nPrepare these items in advance:\n- a clear CV focused on patient care, mobility support, hygiene, monitoring and communication\n- a short cover letter you can tailor by employer\n- passport bio page\n- academic certificates and transcripts\n- professional license or eligibility documents where relevant\n- work references with phone or email contacts\n- police clearance and vaccination records if your target market often requests them\n\nName your files professionally and keep them in PDF where possible. Save a master folder for sea jobs and another for healthcare or caregiver jobs because the supporting documents are usually different.\n\nThis sounds small, but it changes the pace of your search. Official employers often move quickly once a role opens, and applicants with a complete document pack avoid losing time to preventable admin work.\n\nYour goal is not just to apply more often. It is to apply cleanly, directly and with fewer avoidable delays.",
            'category' => 'application_tips',
            'author_name' => 'Global Path Africa Team',
            'cover_icon' => 'fas fa-folder-open',
            'reading_time_minutes' => 4,
            'is_featured' => 0,
            'published_at' => '2026-04-12 09:00:00',
        ],
    ];

    $stmt = $db->prepare("
        INSERT INTO blog_posts (
            title, slug, excerpt, content, category, author_name, cover_icon,
            reading_time_minutes, is_featured, is_active, published_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
    ");

    foreach ($posts as $post) {
        $stmt->execute([
            $post['title'],
            $post['slug'],
            $post['excerpt'],
            $post['content'],
            $post['category'],
            $post['author_name'],
            $post['cover_icon'],
            $post['reading_time_minutes'],
            $post['is_featured'],
            $post['published_at'],
        ]);
    }
}

function seedDefaultJobResources(PDO $db) {
    $existing = (int)$db->query("SELECT COUNT(*) FROM job_resources")->fetchColumn();
    if ($existing > 0) {
        return;
    }

    $resources = [
        [
            'resource_key' => 'msc_cruises_shipboard',
            'title' => 'MSC Cruises Shipboard Careers',
            'organization' => 'MSC Cruises',
            'category' => 'sea_jobs',
            'region' => 'Global',
            'country' => 'Global',
            'resource_type' => 'official_employer',
            'summary' => 'Official shipboard careers portal for hospitality, marine, technical and guest-service roles at sea.',
            'apply_url' => 'https://careers.msccruises.com/onboard-jobs/',
            'application_cost_type' => 'free',
            'cost_notes' => 'Official MSC guidance says neither MSC Cruises nor its recruitment partners charge recruitment fees. Applicants may still pay for visas, medical exams or certifications later when required.',
            'is_featured' => 1,
            'sort_order' => 10,
        ],
        [
            'resource_key' => 'royal_caribbean_ship',
            'title' => 'Royal Caribbean Group Careers at Sea',
            'organization' => 'Royal Caribbean Group',
            'category' => 'sea_jobs',
            'region' => 'Global',
            'country' => 'Global',
            'resource_type' => 'official_employer',
            'summary' => 'Direct shipboard application page covering marine, hotel operations, entertainment, food service and onboard support roles.',
            'apply_url' => 'https://careers.royalcaribbeangroup.com/ship',
            'application_cost_type' => 'free',
            'cost_notes' => 'Royal Caribbean states candidates should not pay any fees to apply, interview or secure employment. Later visa and compliance steps can still apply depending on assignment.',
            'is_featured' => 1,
            'sort_order' => 20,
        ],
        [
            'resource_key' => 'maersk_seafarers',
            'title' => 'Maersk Seafarers Careers',
            'organization' => 'Maersk',
            'category' => 'sea_jobs',
            'region' => 'Global',
            'country' => 'Global',
            'resource_type' => 'official_employer',
            'summary' => 'Official Maersk seafarers page for cadets and experienced crew looking for ocean-going maritime roles.',
            'apply_url' => 'https://www.maersk.com/careers/our-teams/seafarers',
            'application_cost_type' => 'free',
            'cost_notes' => 'Maersk says its recruitment process starts on its own job portal and that applicants should never be asked for money during recruitment.',
            'is_featured' => 1,
            'sort_order' => 30,
        ],
        [
            'resource_key' => 'mediclinic_middle_east',
            'title' => 'Mediclinic Middle East Careers',
            'organization' => 'Mediclinic Middle East',
            'category' => 'caregiver_jobs',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_employer',
            'summary' => 'Direct healthcare careers portal for nurses, allied health, support and administrative roles across Mediclinic facilities in the UAE.',
            'apply_url' => 'https://careers.mediclinic.com/MiddleEast',
            'application_cost_type' => 'free',
            'cost_notes' => 'Mediclinic accepts applications through its online careers portal. No application fee is disclosed, but the employer notes that licensing and immigration rules can affect onboarding time.',
            'is_featured' => 1,
            'sort_order' => 40,
        ],
        [
            'resource_key' => 'cleveland_clinic_abudhabi',
            'title' => 'Cleveland Clinic Abu Dhabi Nursing Careers',
            'organization' => 'Cleveland Clinic Abu Dhabi',
            'category' => 'caregiver_jobs',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_employer',
            'summary' => 'Official Abu Dhabi nursing recruitment page with specialty openings and employer-provided relocation details.',
            'apply_url' => 'https://www.clevelandclinicabudhabi.ae/en/careers/careers-opportunities/nursing-at-cleveland-clinic-abu-dhabi',
            'application_cost_type' => 'free',
            'cost_notes' => 'The application route is direct. The official page says nursing benefits can include relocation expenses covering licensing, visa and flights for successful hires.',
            'is_featured' => 1,
            'sort_order' => 50,
        ],
        [
            'resource_key' => 'burjeel_holdings_careers',
            'title' => 'Burjeel Holdings Careers',
            'organization' => 'Burjeel Holdings',
            'category' => 'caregiver_jobs',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_employer',
            'summary' => 'Official MENA healthcare employer portal with nursing, allied health and patient-service openings.',
            'apply_url' => 'https://burjeelholdings.com/careers/',
            'application_cost_type' => 'free',
            'cost_notes' => 'Burjeel explicitly says it does not authorize payments or fees from applicants and does not use third-party recruiters to sell jobs.',
            'is_featured' => 1,
            'sort_order' => 60,
        ],
        [
            'resource_key' => 'aster_dm_careers',
            'title' => 'Aster DM Healthcare Careers',
            'organization' => 'Aster DM Healthcare',
            'category' => 'caregiver_jobs',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_employer',
            'summary' => 'Direct careers hub for clinical, nursing, allied health and non-clinical roles across Aster facilities in the Gulf.',
            'apply_url' => 'https://www.asterdmhealthcare.com/about-us/careers',
            'application_cost_type' => 'free',
            'cost_notes' => 'Aster runs a direct careers portal. No application fee is disclosed on the official site, though role-specific licensing or visa costs may still appear later.',
            'is_featured' => 0,
            'sort_order' => 70,
        ],
        [
            'resource_key' => 'emirates_health_services',
            'title' => 'Emirates Health Services Careers',
            'organization' => 'Emirates Health Services',
            'category' => 'caregiver_jobs',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_government',
            'summary' => 'Official UAE public-sector healthcare careers page for nurses, allied health professionals and operational staff.',
            'apply_url' => 'https://www.ehs.gov.ae/en/about-us/careers',
            'application_cost_type' => 'free',
            'cost_notes' => 'This is the official public-sector employer route. No application fee is disclosed for the recruitment step itself.',
            'is_featured' => 0,
            'sort_order' => 80,
        ],
        [
            'resource_key' => 'dha_licensing',
            'title' => 'Dubai Health Authority Professional Licensing',
            'organization' => 'Dubai Health Authority',
            'category' => 'licensing',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_regulator',
            'summary' => 'Official Dubai licensing route for healthcare professionals whose roles require registration and license activation.',
            'apply_url' => 'https://dha.gov.ae/en/services/details?id=274&segment=health_facilities_services',
            'application_cost_type' => 'paid',
            'cost_notes' => 'DHA publishes license fees on the official service page. Example listed fees include AED 1,000 for a 1-year nurse or allied full-time license, so treat this as an official paid compliance step rather than a job-access fee.',
            'is_featured' => 1,
            'sort_order' => 90,
        ],
        [
            'resource_key' => 'doh_abudhabi_licensing',
            'title' => 'Department of Health - Abu Dhabi Licensing via TAMM',
            'organization' => 'Department of Health - Abu Dhabi',
            'category' => 'licensing',
            'region' => 'Middle East',
            'country' => 'United Arab Emirates',
            'resource_type' => 'official_regulator',
            'summary' => 'Official Abu Dhabi regulator entry point for healthcare professional licensing and related exam or verification workflows.',
            'apply_url' => 'https://www.doh.gov.ae/en/eservices',
            'application_cost_type' => 'paid',
            'cost_notes' => 'The official FAQ explains that candidates complete verification and book professional exams through TAMM, including payment steps. Confirm the exact current charges inside the official portal before paying.',
            'is_featured' => 0,
            'sort_order' => 100,
        ],
    ];

    $stmt = $db->prepare("
        INSERT INTO job_resources (
            resource_key, title, organization, category, region, country, resource_type,
            summary, apply_url, application_cost_type, cost_notes, is_featured, is_active, sort_order
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
    ");

    foreach ($resources as $resource) {
        $stmt->execute([
            $resource['resource_key'],
            $resource['title'],
            $resource['organization'],
            $resource['category'],
            $resource['region'],
            $resource['country'],
            $resource['resource_type'],
            $resource['summary'],
            $resource['apply_url'],
            $resource['application_cost_type'],
            $resource['cost_notes'],
            $resource['is_featured'],
            $resource['sort_order'],
        ]);
    }
}

function slugify($value) {
    $value = strtolower(trim((string)$value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim($value, '-');
    return $value ?: 'post';
}

function buildUniqueValue(PDO $db, $table, $column, $baseValue, $id = 0) {
    if (!preg_match('/^[a-z_]+$/', $table) || !preg_match('/^[a-z_]+$/', $column)) {
        throw new InvalidArgumentException('Unsafe table or column name.');
    }

    $baseValue = trim((string)$baseValue);
    $baseValue = $baseValue !== '' ? $baseValue : 'item';
    $candidate = $baseValue;
    $suffix = 2;

    while (true) {
        $sql = "SELECT id FROM {$table} WHERE {$column} = ?";
        $params = [$candidate];

        if ($id > 0) {
            $sql .= " AND id != ?";
            $params[] = $id;
        }

        $sql .= " LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $candidate;
        }

        $candidate = $baseValue . '-' . $suffix;
        $suffix++;
    }
}

function blogCategoryLabel($category) {
    $labels = [
        'sea_jobs' => 'Sea Jobs',
        'caregiver_jobs' => 'Caregiver Jobs',
        'application_tips' => 'Application Tips',
        'guides' => 'Guides',
    ];

    return $labels[$category] ?? ucwords(str_replace('_', ' ', $category));
}

function resourceCategoryLabel($category) {
    $labels = [
        'sea_jobs' => 'Sea Jobs',
        'caregiver_jobs' => 'Caregiver Jobs',
        'licensing' => 'Licensing',
        'general_jobs' => 'General Jobs',
    ];

    return $labels[$category] ?? ucwords(str_replace('_', ' ', $category));
}

function resourceTypeLabel($type) {
    $labels = [
        'official_employer' => 'Official Employer Portal',
        'official_government' => 'Official Government Employer',
        'official_regulator' => 'Official Regulator',
    ];

    return $labels[$type] ?? ucwords(str_replace('_', ' ', $type));
}

function jobResourceCostMeta($type) {
    $map = [
        'free' => [
            'label' => 'Free Application',
            'class' => 'success',
            'description' => 'No application fee on the official application route.',
        ],
        'paid' => [
            'label' => 'Paid Requirement',
            'class' => 'warning',
            'description' => 'Official licensing, exam or regulatory payment required.',
        ],
        'mixed' => [
            'label' => 'Mixed Costs',
            'class' => 'info',
            'description' => 'Application may be free, but official downstream costs can apply.',
        ],
    ];

    return $map[$type] ?? $map['free'];
}

function fetchRecentBlogPosts(PDO $db, $limit = 3, $featuredOnly = false) {
    $limit = max(1, (int)$limit);
    $where = $featuredOnly ? "AND is_featured = 1" : '';
    return $db->query("
        SELECT *
        FROM blog_posts
        WHERE is_active = 1 {$where}
        ORDER BY COALESCE(published_at, created_at) DESC, id DESC
        LIMIT {$limit}
    ")->fetchAll();
}

function fetchBlogPosts(PDO $db, $search = '', $category = '') {
    $where = ['is_active = 1'];
    $params = [];

    if ($search !== '') {
        $where[] = '(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)';
        $needle = '%' . $search . '%';
        $params[] = $needle;
        $params[] = $needle;
        $params[] = $needle;
    }

    if ($category !== '') {
        $where[] = 'category = ?';
        $params[] = $category;
    }

    $stmt = $db->prepare("
        SELECT *
        FROM blog_posts
        WHERE " . implode(' AND ', $where) . "
        ORDER BY COALESCE(published_at, created_at) DESC, id DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function findBlogPost(PDO $db, $slug, $id = 0) {
    if ($slug !== '') {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
        if ($post) {
            return $post;
        }
    }

    if ($id > 0) {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    return null;
}

function fetchJobResources(PDO $db, $search = '', $category = '', $cost = '', $featuredOnly = false) {
    $where = ['is_active = 1'];
    $params = [];

    if ($featuredOnly) {
        $where[] = 'is_featured = 1';
    }

    if ($search !== '') {
        $where[] = '(title LIKE ? OR organization LIKE ? OR summary LIKE ? OR country LIKE ?)';
        $needle = '%' . $search . '%';
        $params[] = $needle;
        $params[] = $needle;
        $params[] = $needle;
        $params[] = $needle;
    }

    if ($category !== '') {
        $where[] = 'category = ?';
        $params[] = $category;
    }

    if ($cost !== '') {
        $where[] = 'application_cost_type = ?';
        $params[] = $cost;
    }

    $stmt = $db->prepare("
        SELECT *
        FROM job_resources
        WHERE " . implode(' AND ', $where) . "
        ORDER BY is_featured DESC, sort_order ASC, organization ASC, title ASC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchFeaturedJobResources(PDO $db, $limit = 6) {
    $limit = max(1, (int)$limit);
    return $db->query("
        SELECT *
        FROM job_resources
        WHERE is_active = 1
        ORDER BY is_featured DESC, sort_order ASC, organization ASC
        LIMIT {$limit}
    ")->fetchAll();
}

function formatEditorialContent($text) {
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }

    $blocks = preg_split('/(?:\r?\n){2,}/', $text);
    $html = [];

    foreach ($blocks as $block) {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', trim($block)))));
        if (!$lines) {
            continue;
        }

        $isList = true;
        foreach ($lines as $line) {
            if (!preg_match('/^(?:-|\d+\.)\s+/', $line)) {
                $isList = false;
                break;
            }
        }

        if ($isList) {
            $items = [];
            foreach ($lines as $line) {
                $item = preg_replace('/^(?:-|\d+\.)\s+/', '', $line);
                $items[] = '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $html[] = '<ul>' . implode('', $items) . '</ul>';
            continue;
        }

        $html[] = '<p>' . nl2br(htmlspecialchars($block, ENT_QUOTES, 'UTF-8')) . '</p>';
    }

    return implode("\n", $html);
}

function getMembershipTierRank($membershipType) {
    $map = [
        'free' => 0,
        'premium' => 1,
        'premium_plus' => 2,
    ];

    return $map[$membershipType] ?? 0;
}

function getAccessibleJobTiers($membershipType) {
    $rank = getMembershipTierRank($membershipType);
    $tiers = ['free'];

    if ($rank >= 1) {
        $tiers[] = 'premium';
    }

    if ($rank >= 2) {
        $tiers[] = 'premium_plus';
    }

    return $tiers;
}

function userCanAccessTier($membershipType, $requiredTier) {
    $required = getMembershipTierRank($requiredTier === '' ? 'free' : $requiredTier);
    return getMembershipTierRank($membershipType) >= $required;
}

function userCanAccessJob($user, array $job) {
    $membershipType = $user['membership_type'] ?? 'free';
    $requiredTier = $job['access_tier'] ?? (($job['is_premium_only'] ?? 0) ? 'premium' : 'free');
    return userCanAccessTier($membershipType, $requiredTier);
}

function jobAccessTierLabel($tier) {
    $labels = [
        'free' => 'Free Plan',
        'premium' => 'Premium Listing',
        'premium_plus' => 'Premium Plus Listing',
    ];

    return $labels[$tier] ?? 'Free Plan';
}

function jobAccessTierBadgeClass($tier) {
    $classes = [
        'free' => 'badge-gray',
        'premium' => 'badge-blue',
        'premium_plus' => 'badge-premium',
    ];

    return $classes[$tier] ?? 'badge-gray';
}

function getJobOrderBySql($alias = '') {
    $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
    return "CASE WHEN {$prefix}deadline IS NOT NULL AND {$prefix}deadline < CURDATE() THEN 1 ELSE 0 END ASC, COALESCE({$prefix}published_at, {$prefix}updated_at, {$prefix}created_at) DESC, {$prefix}id DESC";
}

function jobDeadlineMeta(array $job) {
    if (empty($job['deadline'])) {
        if (!empty($job['published_at'])) {
            return [
                'label' => 'Updated ' . timeAgo($job['published_at']),
                'class' => 'muted',
            ];
        }

        return [
            'label' => 'Open',
            'class' => 'muted',
        ];
    }

    $deadline = strtotime($job['deadline']);
    if ($deadline === false) {
        return [
            'label' => 'Open',
            'class' => 'muted',
        ];
    }

    $today = strtotime(date('Y-m-d'));
    $daysLeft = (int)floor(($deadline - $today) / 86400);

    if ($daysLeft < 0) {
        return [
            'label' => 'Deadline passed',
            'class' => 'passed',
        ];
    }

    if ($daysLeft <= 7) {
        return [
            'label' => $daysLeft === 0 ? 'Deadline today' : $daysLeft . 'd left',
            'class' => 'urgent',
        ];
    }

    return [
        'label' => date('d M Y', $deadline),
        'class' => 'date',
    ];
}

function hasJobDeadlinePassed(array $job) {
    if (empty($job['deadline'])) {
        return false;
    }

    $deadline = strtotime($job['deadline']);
    if ($deadline === false) {
        return false;
    }

    return $deadline < strtotime(date('Y-m-d'));
}

function getRemoteJobBoards() {
    return [
        [
            'title' => 'FlexJobs',
            'url' => 'https://www.flexjobs.com/pricing.aspx',
            'cost_type' => 'paid',
            'summary' => 'Large remote and flexible work board with subscription access for job seekers.',
            'note' => 'Official pricing currently starts at $2.95 for a 14-day trial on the FlexJobs pricing page.',
        ],
        [
            'title' => 'We Work Remotely',
            'url' => 'https://weworkremotely.com/',
            'cost_type' => 'free',
            'summary' => 'Established remote-only board with public listings across tech, support, marketing and operations.',
            'note' => 'The official site presents browsable public listings and remote-first categories without a job-seeker fee on the homepage.',
        ],
        [
            'title' => 'Remote OK',
            'url' => 'https://remoteok.com/',
            'cost_type' => 'free',
            'summary' => 'Popular remote board focused on programming, design, sales, support and distributed teams.',
            'note' => 'The official Remote OK board shows public listings on the site; no separate job-seeker subscription price is shown on the page we checked.',
        ],
        [
            'title' => 'Wellfound Remote Jobs',
            'url' => 'https://wellfound.com/candidates/remote',
            'cost_type' => 'free',
            'summary' => 'Remote-friendly startup and tech hiring platform with candidate profiles and recruiter discovery.',
            'note' => 'Wellfound\'s official remote candidate page focuses on remote job discovery and recruiter matching without advertising a paid job-seeker tier there.',
        ],
        [
            'title' => 'Jobspresso',
            'url' => 'https://jobspresso.co/',
            'cost_type' => 'free',
            'summary' => 'Curated remote job board spanning developer, design, support, marketing, sales and writing roles.',
            'note' => 'The official Jobspresso homepage exposes browsable remote jobs publicly and positions itself as a curated remote board.',
        ],
        [
            'title' => 'LinkedIn Remote Jobs',
            'url' => 'https://www.linkedin.com/jobs/remote-jobs',
            'cost_type' => 'mixed',
            'summary' => 'Mainstream job marketplace with a large remote filter and broad employer coverage.',
            'note' => 'LinkedIn offers remote job search from its jobs product; applying usually requires a LinkedIn account, but the search experience is widely accessible.',
        ],
    ];
}
