<?php
$pageTitle = 'Blog Detail';
require_once 'includes/config.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

$db = getDB();
bootSiteContent($db);

$slug = sanitize($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);
$post = findBlogPost($db, $slug, $id);

if (!$post) {
    redirect(SITE_URL . '/blog.php');
}

$pageTitle = $post['title'];
$relatedStmt = $db->prepare("
    SELECT *
    FROM blog_posts
    WHERE is_active = 1 AND id != ?
    ORDER BY COALESCE(published_at, created_at) DESC, id DESC
    LIMIT 3
");
$relatedStmt->execute([$post['id']]);
$relatedPosts = $relatedStmt->fetchAll();

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right" style="font-size:.65rem"></i>
            <a href="blog.php">Blog</a>
            <i class="fas fa-chevron-right" style="font-size:.65rem"></i>
            <span><?= htmlspecialchars($post['title']) ?></span>
        </div>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p><?= htmlspecialchars($post['excerpt']) ?></p>
    </div>
</div>

<section>
    <div class="container">
        <div class="article-shell">
            <article class="article-main">
                <div class="article-meta-row">
                    <span class="blog-chip"><?= htmlspecialchars(blogCategoryLabel($post['category'])) ?></span>
                    <span><i class="fas fa-clock"></i> <?= (int)$post['reading_time_minutes'] ?> min read</span>
                    <span><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></span>
                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($post['author_name']) ?></span>
                </div>
                <div class="article-icon"><i class="<?= htmlspecialchars($post['cover_icon']) ?>"></i></div>
                <div class="article-content">
                    <?= formatEditorialContent($post['content']) ?>
                </div>
            </article>

            <aside class="article-sidebar">
                <div class="article-panel">
                    <h3>Apply smarter</h3>
                    <p>Use the resource directory to move from advice to direct action using official employer and regulator links.</p>
                    <a href="job-resources.php" class="btn btn-primary btn-block" style="justify-content:center;">Open Job Resources</a>
                </div>
                <div class="article-panel">
                    <h3>More articles</h3>
                    <div class="article-list">
                        <?php foreach($relatedPosts as $related): ?>
                        <a href="blog-detail.php?slug=<?= urlencode($related['slug']) ?>" class="article-list-item">
                            <strong><?= htmlspecialchars($related['title']) ?></strong>
                            <span><?= htmlspecialchars(blogCategoryLabel($related['category'])) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
