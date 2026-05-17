<?php
$pageTitle = 'Blog';
require_once 'includes/config.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

$db = getDB();
bootSiteContent($db);

$search = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$posts = fetchBlogPosts($db, $search, $category);
$categories = [
    'sea_jobs' => 'Sea Jobs',
    'caregiver_jobs' => 'Caregiver Jobs',
    'application_tips' => 'Application Tips',
];

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><span>Blog</span></div>
        <h1><i class="fas fa-newspaper"></i> Application Blog</h1>
        <p>Practical reads on sea jobs, caregiver roles, scam avoidance and applying directly through official channels.</p>
    </div>
</div>

<section>
    <div class="container">
        <div style="background:white;padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow-sm);margin-bottom:2rem;border:1px solid var(--border);">
            <form method="GET" class="search-bar">
                <div class="search-input-wrap"><i class="fas fa-search"></i><input type="text" name="search" placeholder="Search articles..." value="<?= htmlspecialchars($search) ?>"></div>
                <select name="category" class="filter-select">
                    <option value="">All Topics</option>
                    <?php foreach($categories as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-green"><i class="fas fa-filter"></i> Filter</button>
                <?php if($search || $category): ?><a href="blog.php" class="btn btn-outline">Clear</a><?php endif; ?>
            </form>
        </div>

        <?php if($posts): ?>
        <div class="blog-grid">
            <?php foreach($posts as $post): ?>
            <article class="blog-card reveal">
                <div class="blog-icon"><i class="<?= htmlspecialchars($post['cover_icon']) ?>"></i></div>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span class="blog-chip"><?= htmlspecialchars(blogCategoryLabel($post['category'])) ?></span>
                        <span><i class="fas fa-clock"></i> <?= (int)$post['reading_time_minutes'] ?> min read</span>
                    </div>
                    <h3><a href="blog-detail.php?slug=<?= urlencode($post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                    <p><?= htmlspecialchars($post['excerpt']) ?></p>
                </div>
                <div class="blog-card-footer">
                    <span><?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></span>
                    <a href="blog-detail.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-outline btn-sm">Read Article</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:4rem 2rem;background:white;border-radius:var(--radius);border:1px solid var(--border);">
            <div style="font-size:3rem;margin-bottom:1rem;"><i class="fas fa-newspaper"></i></div>
            <h3 style="font-family:var(--font-display);margin-bottom:.5rem;">No articles found</h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;">Try a different keyword or clear the topic filter.</p>
            <a href="blog.php" class="btn btn-green">View All Articles</a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
