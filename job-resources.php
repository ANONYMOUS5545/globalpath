<?php
$pageTitle = 'Job Resources';
require_once 'includes/config.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

$db = getDB();
bootSiteContent($db);

$search = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$cost = sanitize($_GET['cost'] ?? '');
$resources = fetchJobResources($db, $search, $category, $cost);

$categories = [
    'sea_jobs' => 'Sea Jobs',
    'caregiver_jobs' => 'Caregiver Jobs',
    'licensing' => 'Licensing',
];

$costOptions = [
    'free' => 'Free Application',
    'paid' => 'Paid Requirement',
    'mixed' => 'Mixed Costs',
];

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><span>Job Resources</span></div>
        <h1><i class="fas fa-link"></i> Job Application Resources</h1>
        <p>Direct employer and regulator portals for sea jobs, caregiver hiring and Middle East compliance steps.</p>
    </div>
</div>

<section>
    <div class="container">
        <div class="resource-legend">
            <?php foreach(['free', 'paid', 'mixed'] as $legendKey): $legend = jobResourceCostMeta($legendKey); ?>
            <div class="legend-card legend-card-<?= htmlspecialchars($legend['class']) ?>">
                <strong><?= htmlspecialchars($legend['label']) ?></strong>
                <span><?= htmlspecialchars($legend['description']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="background:white;padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow-sm);margin-bottom:2rem;border:1px solid var(--border);">
            <form method="GET" class="search-bar">
                <div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search organisations, countries or job tracks..." value="<?= htmlspecialchars($search) ?>"></div>
                <select name="category" class="filter-select">
                    <option value="">All Tracks</option>
                    <?php foreach($categories as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="cost" class="filter-select">
                    <option value="">All Cost Types</option>
                    <?php foreach($costOptions as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= $cost === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-green"><i class="fas fa-filter"></i> Filter</button>
                <?php if($search || $category || $cost): ?><a href="job-resources.php" class="btn btn-outline">Clear</a><?php endif; ?>
            </form>
        </div>

        <?php if($resources): ?>
        <div class="resource-grid">
            <?php foreach($resources as $resource): $meta = jobResourceCostMeta($resource['application_cost_type']); ?>
            <article class="resource-card reveal">
                <div class="resource-card-top">
                    <div>
                        <div class="resource-kicker"><?= htmlspecialchars(resourceTypeLabel($resource['resource_type'])) ?></div>
                        <h3><?= htmlspecialchars($resource['title']) ?></h3>
                    </div>
                    <span class="cost-pill cost-pill-<?= htmlspecialchars($meta['class']) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                </div>
                <div class="resource-meta-row">
                    <span><i class="fas fa-building"></i> <?= htmlspecialchars($resource['organization']) ?></span>
                    <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars(resourceCategoryLabel($resource['category'])) ?></span>
                    <span><i class="fas fa-location-dot"></i> <?= htmlspecialchars($resource['country'] ?: $resource['region']) ?></span>
                </div>
                <p><?= htmlspecialchars($resource['summary']) ?></p>
                <div class="resource-note">
                    <strong>How we classify this:</strong> <?= htmlspecialchars($resource['cost_notes']) ?>
                </div>
                <div class="resource-actions">
                    <a href="<?= htmlspecialchars($resource['apply_url']) ?>" target="_blank" class="btn btn-green btn-sm">Open Official Site</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:4rem 2rem;background:white;border-radius:var(--radius);border:1px solid var(--border);">
            <div style="font-size:3rem;margin-bottom:1rem;"><i class="fas fa-link"></i></div>
            <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">No resources found</h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Try a broader search or clear the current filters.</p>
            <a href="job-resources.php" class="btn btn-green">View All Resources</a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
