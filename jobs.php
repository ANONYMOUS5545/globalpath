<?php
$pageTitle = 'Jobs Abroad';
require_once 'includes/config.php';
require_once 'includes/opportunity_sync.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

$user = getCurrentUser();
$membershipType = $user['membership_type'] ?? 'free';
$accessibleTiers = getAccessibleJobTiers($membershipType);
$db = getDB();
bootSiteContent($db);
$jobSync = bootOpportunitySync($db, 'job');

$search = sanitize($_GET['search'] ?? '');
$sector = sanitize($_GET['sector'] ?? '');
$country = sanitize($_GET['country'] ?? '');
$type = sanitize($_GET['type'] ?? '');
$tierFilter = sanitize($_GET['tier'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;
$remoteBoards = array_slice(getRemoteJobBoards(), 0, 4);

$whereBase = ['is_active = 1'];
$paramsBase = [];

if ($search !== '') {
    $whereBase[] = "(title LIKE ? OR description LIKE ? OR organization LIKE ?)";
    $needle = "%{$search}%";
    $paramsBase[] = $needle;
    $paramsBase[] = $needle;
    $paramsBase[] = $needle;
}

if ($sector !== '') {
    $whereBase[] = "sector LIKE ?";
    $paramsBase[] = "%{$sector}%";
}

if ($country !== '') {
    $whereBase[] = "country LIKE ?";
    $paramsBase[] = "%{$country}%";
}

if ($type !== '') {
    $whereBase[] = "job_type = ?";
    $paramsBase[] = $type;
}

$whereTier = $whereBase;
$paramsTier = $paramsBase;

if ($tierFilter !== '' && in_array($tierFilter, $accessibleTiers, true)) {
    $whereTier[] = "access_tier = ?";
    $paramsTier[] = $tierFilter;
} else {
    $placeholders = implode(',', array_fill(0, count($accessibleTiers), '?'));
    $whereTier[] = "access_tier IN ({$placeholders})";
    $paramsTier = array_merge($paramsTier, $accessibleTiers);
}

$whereBaseSql = 'WHERE ' . implode(' AND ', $whereBase);
$whereTierSql = 'WHERE ' . implode(' AND ', $whereTier);

$tierCounts = [
    'free' => 0,
    'premium' => 0,
    'premium_plus' => 0,
];
$tierCountStmt = $db->prepare("
    SELECT access_tier, COUNT(*) AS total
    FROM jobs
    {$whereBaseSql}
    GROUP BY access_tier
");
$tierCountStmt->execute($paramsBase);
foreach ($tierCountStmt->fetchAll() as $row) {
    $tierCounts[$row['access_tier']] = (int)$row['total'];
}

$lockedCounts = [
    'premium' => !in_array('premium', $accessibleTiers, true) ? ($tierCounts['premium'] ?? 0) : 0,
    'premium_plus' => !in_array('premium_plus', $accessibleTiers, true) ? ($tierCounts['premium_plus'] ?? 0) : 0,
];

$countStmt = $db->prepare("SELECT COUNT(*) FROM jobs {$whereTierSql}");
$countStmt->execute($paramsTier);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$stmt = $db->prepare("
    SELECT *
    FROM jobs
    {$whereTierSql}
    ORDER BY " . getJobOrderBySql() . "
    LIMIT {$perPage} OFFSET {$offset}
");
$stmt->execute($paramsTier);
$jobs = $stmt->fetchAll();

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><span>Jobs Abroad</span></div>
        <h1><i class="fas fa-briefcase"></i> International Jobs for Africans</h1>
        <p>Most recent active jobs appear first, while expired deadlines stay visible but are clearly marked.</p>
    </div>
</div>

<section>
<div class="container">
    <?php if (isset($_GET['locked'])): ?>
    <div class="alert alert-error" style="margin-bottom:1.5rem;">
        This listing needs a higher membership tier than your current plan.
    </div>
    <?php endif; ?>

    <div style="background:white;padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow-sm);margin-bottom:2rem;border:1px solid var(--border);">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search jobs, organisations..." value="<?= htmlspecialchars($search) ?>"></div>
            <select name="sector" class="filter-select">
                <option value="">All Sectors</option>
                <?php foreach(['International Development','Healthcare','Technology','Research & Economics','Environment & Climate','Education','NGO & Non-Profit','Remote Work'] as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $sector === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="type" class="filter-select">
                <option value="">All Types</option>
                <option value="full_time" <?= $type === 'full_time' ? 'selected' : '' ?>>Full Time</option>
                <option value="part_time" <?= $type === 'part_time' ? 'selected' : '' ?>>Part Time</option>
                <option value="contract" <?= $type === 'contract' ? 'selected' : '' ?>>Contract</option>
                <option value="internship" <?= $type === 'internship' ? 'selected' : '' ?>>Internship</option>
                <option value="volunteer" <?= $type === 'volunteer' ? 'selected' : '' ?>>Volunteer</option>
            </select>
            <select name="tier" class="filter-select">
                <option value="">All Visible Plans</option>
                <?php foreach($accessibleTiers as $tier): ?>
                <option value="<?= htmlspecialchars($tier) ?>" <?= $tierFilter === $tier ? 'selected' : '' ?>><?= htmlspecialchars(jobAccessTierLabel($tier)) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-green"><i class="fas fa-filter"></i> Filter</button>
            <?php if($search || $sector || $type || $tierFilter): ?><a href="jobs.php" class="btn btn-outline">Clear</a><?php endif; ?>
        </form>
    </div>

    <?php if ($membershipType === 'free'): ?>
    <div class="resource-banner">
        <div>
            <span class="resource-banner-kicker">Plan Access</span>
            <h3>Your free plan shows free listings only.</h3>
            <p>
                Premium currently unlocks <?= number_format($lockedCounts['premium']) ?> more listings
                and Premium Plus unlocks <?= number_format($lockedCounts['premium_plus']) ?> additional higher-tier listings,
                plus CV drafting support and hands-on job application help.
            </p>
        </div>
        <a href="membership.php#premium" class="btn btn-primary">Upgrade</a>
    </div>
    <?php elseif ($membershipType === 'premium'): ?>
    <div class="resource-banner">
        <div>
            <span class="resource-banner-kicker">Plan Access</span>
            <h3>Your Premium plan includes premium listings.</h3>
            <p>
                You can view both free and premium jobs here. Premium Plus currently unlocks
                <?= number_format($lockedCounts['premium_plus']) ?> more higher-tier listings together with deeper CV and application support.
            </p>
        </div>
        <a href="membership.php#premium-plus" class="btn btn-primary">See Premium Plus</a>
    </div>
    <?php else: ?>
    <div class="resource-banner">
        <div>
            <span class="resource-banner-kicker">Plan Access</span>
            <h3>Your Premium Plus plan can view every listing tier.</h3>
            <p>You also have access to CV drafting help, cover-letter refinement and faster support across healthcare, sea jobs, remote roles, NGO and tech applications.</p>
        </div>
        <a href="membership.php#cv-support" class="btn btn-primary">CV Support</a>
    </div>
    <?php endif; ?>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;">
        <p style="color:var(--text-muted);">
            Showing <strong><?= count($jobs) ?></strong> of <strong><?= number_format($total) ?></strong> jobs available on your current plan
            <?php if (!empty($jobSync['last_success_at'])): ?>
                <span style="display:block;font-size:.82rem;margin-top:.35rem;">Live feeds refreshed <?= htmlspecialchars(timeAgo($jobSync['last_success_at'])) ?></span>
            <?php endif; ?>
        </p>
        <div style="font-size:.85rem;color:var(--text-muted);display:flex;align-items:center;gap:.45rem;">
            <i class="fas fa-shield-alt" style="color:var(--primary);"></i>
            Source: official employer hiring pages only
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <?php foreach($accessibleTiers as $tier): ?>
            <span class="badge <?= jobAccessTierBadgeClass($tier) ?>"><?= htmlspecialchars(jobAccessTierLabel($tier)) ?>: <?= number_format($tierCounts[$tier] ?? 0) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if($jobs): ?>
    <div class="grid-3">
    <?php foreach($jobs as $j): $deadlineMeta = jobDeadlineMeta($j); $jobTier = $j['access_tier'] ?? (($j['is_premium_only'] ?? 0) ? 'premium' : 'free'); ?>
    <div class="card reveal">
        <div class="card-body" style="padding-top:1.75rem;">
            <div class="card-meta">
                <span class="badge <?= jobAccessTierBadgeClass($jobTier) ?>"><?= htmlspecialchars(jobAccessTierLabel($jobTier)) ?></span>
                <?php if(($j['listing_origin'] ?? 'manual') === 'imported'): ?><span class="badge badge-green"><i class="fas fa-signal"></i> Live Feed</span><?php endif; ?>
                <span class="badge badge-blue"><?= str_replace('_',' ',ucfirst($j['job_type'])) ?></span>
                <span class="badge badge-gray"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($j['country']) ?></span>
            </div>
            <h3><a href="job-detail.php?id=<?= $j['id'] ?>"><?= htmlspecialchars($j['title']) ?></a></h3>
            <p style="font-weight:600;color:var(--primary);font-size:.85rem;margin-bottom:.5rem;"><?= htmlspecialchars($j['organization']) ?></p>
            <?php if(!empty($j['source_org'])): ?><p style="font-size:.78rem;color:var(--text-muted);margin-bottom:.4rem;"><i class="fas fa-rss"></i> <?= htmlspecialchars($j['source_org']) ?></p><?php endif; ?>
            <?php if($j['sector']): ?><p style="font-size:.78rem;color:var(--text-muted);margin-bottom:.5rem;"><i class="fas fa-tag"></i> <?= htmlspecialchars($j['sector']) ?></p><?php endif; ?>
            <p><?= htmlspecialchars(substr($j['description'],0,110)) ?>...</p>
            <?php if($j['salary_range']): ?><p style="font-size:.8rem;color:var(--primary);font-weight:600;"><i class="fas fa-dollar-sign"></i> <?= htmlspecialchars($j['salary_range']) ?></p><?php endif; ?>
            <div class="card-footer">
                <span class="card-deadline">
                    <i class="fas fa-clock"></i>
                    <?php if ($deadlineMeta['class'] === 'passed'): ?>
                        <span style="color:var(--accent);font-weight:700;"><?= htmlspecialchars($deadlineMeta['label']) ?></span>
                    <?php elseif ($deadlineMeta['class'] === 'urgent'): ?>
                        <span style="color:var(--accent);font-weight:700;"><?= htmlspecialchars($deadlineMeta['label']) ?></span>
                    <?php else: ?>
                        <?= htmlspecialchars($deadlineMeta['label']) ?>
                    <?php endif; ?>
                </span>
                <a href="job-detail.php?id=<?= $j['id'] ?>" class="btn btn-green btn-sm">View Job</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <?php if($totalPages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?><a href="?page=<?=$page-1?>&search=<?=urlencode($search)?>&sector=<?=urlencode($sector)?>&type=<?=urlencode($type)?>&tier=<?=urlencode($tierFilter)?>" class="page-btn"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
        <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?><a href="?page=<?=$i?>&search=<?=urlencode($search)?>&sector=<?=urlencode($sector)?>&type=<?=urlencode($type)?>&tier=<?=urlencode($tierFilter)?>" class="page-btn <?=$i==$page?'active':''?>"><?=$i?></a><?php endfor; ?>
        <?php if($page < $totalPages): ?><a href="?page=<?=$page+1?>&search=<?=urlencode($search)?>&sector=<?=urlencode($sector)?>&type=<?=urlencode($type)?>&tier=<?=urlencode($tierFilter)?>" class="page-btn"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div style="text-align:center;padding:5rem 2rem;background:white;border-radius:var(--radius);border:1px solid var(--border);">
        <div style="font-size:4rem;margin-bottom:1rem;">💼</div>
        <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">No Jobs Found</h3>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">Try adjusting your filters or plan selection.</p>
        <a href="jobs.php" class="btn btn-green">View All Available Jobs</a>
    </div>
    <?php endif; ?>

    <div class="section-header reveal" style="margin-top:4rem;">
        <span class="section-badge">Remote Jobs</span>
        <h2 class="section-title">Trusted Remote Job Boards</h2>
        <p class="section-subtitle">Popular remote platforms with clear cost notes, including paid boards like FlexJobs and free or mixed-access boards with strong reputations.</p>
    </div>
    <div class="resource-grid">
        <?php foreach($remoteBoards as $board): $costMeta = jobResourceCostMeta($board['cost_type']); ?>
        <article class="resource-card reveal">
            <div class="resource-card-top">
                <div>
                    <div class="resource-kicker">Remote Jobs</div>
                    <h3><?= htmlspecialchars($board['title']) ?></h3>
                </div>
                <span class="cost-pill cost-pill-<?= htmlspecialchars($costMeta['class']) ?>"><?= htmlspecialchars($costMeta['label']) ?></span>
            </div>
            <p><?= htmlspecialchars($board['summary']) ?></p>
            <div class="resource-note">
                <strong>Cost note:</strong> <?= htmlspecialchars($board['note']) ?>
            </div>
            <div class="resource-actions">
                <a href="<?= htmlspecialchars($board['url']) ?>" target="_blank" class="btn btn-green btn-sm">Open Site</a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="remote-jobs.php" class="btn btn-outline btn-lg">Browse Remote Job Boards <i class="fas fa-arrow-right"></i></a>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
