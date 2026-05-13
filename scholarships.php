<?php
$pageTitle = 'Scholarships';
require_once 'includes/config.php';
require_once 'includes/opportunity_sync.php';
startSecureSession();
$user = getCurrentUser();

$db = getDB();
$scholarshipSync = bootOpportunitySync($db, 'scholarship');

// Filters
$search  = sanitize($_GET['search'] ?? '');
$level   = sanitize($_GET['level'] ?? '');
$country = sanitize($_GET['country'] ?? '');
$type    = sanitize($_GET['type'] ?? '');
$source  = sanitize($_GET['source'] ?? '');

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;

$where  = ['is_active = 1'];
$params = [];

if ($search) {
    $where[] = "(title LIKE ? OR description LIKE ? OR provider LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}
if ($level)   { $where[] = "level = ?";   $params[] = $level; }
if ($country) { $where[] = "country LIKE ?"; $params[] = "%$country%"; }
if ($type)    { $where[] = "type = ?";    $params[] = $type; }
if ($source)  { $where[] = "(LOWER(provider) LIKE ? OR LOWER(source_org) LIKE ?)"; $params[] = "%$source%"; $params[] = "%$source%"; }

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $db->prepare("SELECT COUNT(*) FROM scholarships $whereSQL");
$countStmt->execute($params);
$totalCount = $countStmt->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

$listStmt = $db->prepare("SELECT * FROM scholarships $whereSQL ORDER BY COALESCE(published_at, updated_at, created_at) DESC, id DESC LIMIT $perPage OFFSET $offset");
$listStmt->execute($params);
$scholarships = $listStmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right" style="font-size:0.65rem;"></i>
            <span>Scholarships</span>
        </div>
        <h1><i class="fas fa-graduation-cap"></i> International Scholarships for Africans</h1>
        <p>Newest scholarship openings first, based on the latest published or updated records in our feed</p>
    </div>
</div>

<section>
    <div class="container">
        <!-- Search and Filters -->
        <div style="background:white;padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow-sm);margin-bottom:2rem;border:1px solid var(--border);">
            <form method="GET" class="search-bar">
                <div class="search-input-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search scholarships..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="level" class="filter-select">
                    <option value="">All Levels</option>
                    <option value="undergraduate" <?= $level==='undergraduate'?'selected':'' ?>>Undergraduate</option>
                    <option value="postgraduate" <?= $level==='postgraduate'?'selected':'' ?>>Postgraduate</option>
                    <option value="phd" <?= $level==='phd'?'selected':'' ?>>PhD</option>
                    <option value="all" <?= $level==='all'?'selected':'' ?>>All Levels</option>
                </select>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="full" <?= $type==='full'?'selected':'' ?>>Fully Funded</option>
                    <option value="partial" <?= $type==='partial'?'selected':'' ?>>Partial Funding</option>
                    <option value="fellowship" <?= $type==='fellowship'?'selected':'' ?>>Fellowship</option>
                    <option value="exchange" <?= $type==='exchange'?'selected':'' ?>>Exchange</option>
                </select>
                <select name="country" class="filter-select">
                    <option value="">All Countries</option>
                    <option value="Germany" <?= $country==='Germany'?'selected':'' ?>>Germany</option>
                    <option value="United Kingdom" <?= $country==='United Kingdom'?'selected':'' ?>>United Kingdom</option>
                    <option value="United States" <?= $country==='United States'?'selected':'' ?>>United States</option>
                    <option value="France" <?= $country==='France'?'selected':'' ?>>France</option>
                    <option value="European Union" <?= $country==='European Union'?'selected':'' ?>>EU</option>
                </select>
                <button type="submit" class="btn btn-green"><i class="fas fa-filter"></i> Filter</button>
                <?php if ($search || $level || $country || $type): ?>
                <a href="scholarships.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Results header -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
            <p style="color:var(--text-muted);">
                Showing <strong><?= count($scholarships) ?></strong> of <strong><?= $totalCount ?></strong> scholarships
                <?= $search ? " for \"<strong>" . htmlspecialchars($search) . "</strong>\"" : '' ?>
                <?php if (!empty($scholarshipSync['last_success_at'])): ?>
                <span style="display:block;font-size:0.82rem;margin-top:0.35rem;">Live feeds refreshed <?= htmlspecialchars(timeAgo($scholarshipSync['last_success_at'])) ?></span>
                <?php endif; ?>
            </p>
            <div style="display:flex;gap:0.5rem;align-items:center;font-size:0.85rem;color:var(--text-muted);">
                <i class="fas fa-info-circle"></i>
                Source: official opportunity pages, universities and vetted scholarship feeds
            </div>
        </div>
        
        <!-- Source Quick Filters -->
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;">
            <?php
            $sources = ['All'=>'','Erasmus+'=>'erasmus','DAAD'=>'daad','Chevening'=>'chevening','Fulbright'=>'fulbright','World Bank'=>'world bank','Commonwealth'=>'commonwealth'];
            foreach ($sources as $label => $val):
            ?>
            <a href="?source=<?= urlencode($val) ?>&level=<?= urlencode($level) ?>&type=<?= urlencode($type) ?>"
               class="badge <?= $source === $val ? 'badge-green' : 'badge-gray' ?>" style="padding:0.4rem 0.9rem;text-decoration:none;cursor:pointer;">
               <?= htmlspecialchars($label) ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php if ($scholarships): ?>
        <div class="grid-3">
            <?php foreach ($scholarships as $s): ?>
            <div class="card reveal">
                <div class="card-image" style="position:relative;">
                    <span style="font-size:3rem;"><?= getScholarshipEmoji($s['country']) ?></span>
                    <?php if ($s['is_featured']): ?>
                    <span class="badge badge-gold" style="position:absolute;top:0.75rem;right:0.75rem;"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                    <?php if (($s['listing_origin'] ?? 'manual') === 'imported'): ?>
                    <span class="badge badge-green" style="position:absolute;bottom:0.75rem;left:0.75rem;"><i class="fas fa-signal"></i> Live</span>
                    <?php endif; ?>
                    <?php if ($s['type'] === 'full'): ?>
                    <span class="badge badge-green" style="position:absolute;top:0.75rem;left:0.75rem;">Fully Funded</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span class="badge badge-blue"><?= ucfirst($s['level']) ?></span>
                        <span class="badge badge-gray"><i class="fas fa-flag"></i> <?= htmlspecialchars($s['country']) ?></span>
                    </div>
                    <h3><a href="scholarship-detail.php?id=<?= $s['id'] ?>"><?= htmlspecialchars($s['title']) ?></a></h3>
                    <p style="font-size:0.8rem;font-weight:600;color:var(--primary);margin-bottom:0.4rem;">
                        <?= htmlspecialchars($s['provider']) ?>
                    </p>
                    <?php if (!empty($s['source_org'])): ?>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:0.4rem;"><i class="fas fa-rss"></i> <?= htmlspecialchars($s['source_org']) ?></p>
                    <?php endif; ?>
                    <p><?= htmlspecialchars(substr($s['description'], 0, 110)) ?>...</p>
                    <?php if ($s['field_of_study']): ?>
                    <p style="font-size:0.78rem;color:var(--text-muted);"><i class="fas fa-book"></i> <?= htmlspecialchars($s['field_of_study']) ?></p>
                    <?php endif; ?>
                    <div class="card-footer">
                        <?php if ($s['deadline']): ?>
                        <span class="card-deadline">
                            <i class="fas fa-clock"></i> 
                            <?php
                            $deadline = strtotime($s['deadline']);
                            $daysLeft = floor(($deadline - time()) / 86400);
                            if ($daysLeft < 0) echo 'Closed';
                            elseif ($daysLeft <= 7) echo "<span style='color:var(--accent)'>⚡ {$daysLeft} days left!</span>";
                            else echo date('d M Y', $deadline);
                            ?>
                        </span>
                        <?php elseif (!empty($s['published_at'])): ?>
                        <span class="card-deadline"><i class="fas fa-clock"></i> Updated <?= htmlspecialchars(timeAgo($s['published_at'])) ?></span>
                        <?php endif; ?>
                        <a href="scholarship-detail.php?id=<?= $s['id'] ?>" class="btn btn-green btn-sm">Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&level=<?= $level ?>&type=<?= $type ?>&country=<?= urlencode($country) ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&level=<?= $level ?>&type=<?= $type ?>&country=<?= urlencode($country) ?>" class="page-btn <?= $i==$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&level=<?= $level ?>&type=<?= $type ?>&country=<?= urlencode($country) ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div style="text-align:center;padding:5rem 2rem;background:white;border-radius:var(--radius);border:1px solid var(--border);">
            <div style="font-size:4rem;margin-bottom:1rem;">🔍</div>
            <h3 style="font-family:var(--font-display);margin-bottom:0.5rem;">No Scholarships Found</h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Try adjusting your filters or search terms</p>
            <a href="scholarships.php" class="btn btn-green">View All Scholarships</a>
        </div>
        <?php endif; ?>
        
        <!-- Need more help? -->
        <div style="margin-top:3rem;background:linear-gradient(135deg,#f0faf5,white);border:2px solid var(--primary);border-radius:var(--radius);padding:2rem;text-align:center;">
            <h3 style="font-family:var(--font-display);margin-bottom:0.75rem;">Need Help With Your Scholarship Application?</h3>
            <p style="color:var(--text-muted);margin-bottom:1.25rem;">Our expert team provides guided SOP writing, document review and submission support for just $<?= PRICE_SCHOLARSHIP_SUPPORT ?>.</p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <a href="scholarship-support.php" class="btn btn-primary">Get Scholarship Support</a>
                <a href="<?= WHATSAPP_LINK ?>?text=I+need+help+with+scholarship+applications" target="_blank" class="btn" style="background:#25d366;color:white;">
                    <i class="fab fa-whatsapp"></i> WhatsApp Support
                </a>
            </div>
        </div>
    </div>
</section>

<?php
function getScholarshipEmoji($country) {
    $map = [
        'Germany'=>'🇩🇪','United Kingdom'=>'🇬🇧','United States'=>'🇺🇸',
        'France'=>'🇫🇷','Netherlands'=>'🇳🇱','European Union'=>'🇪🇺',
        'Various Countries'=>'🌍','Switzerland'=>'🇨🇭','Canada'=>'🇨🇦',
    ];
    return $map[$country] ?? '🎓';
}
require_once 'includes/footer.php'; ?>
