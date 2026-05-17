<?php
$adminTitle = 'Manage Blog';
require_once '../includes/content_bootstrap.php';
require_once 'header.php';

$db = getDB();
bootSiteContent($db);

$allowedCategories = [
    'sea_jobs' => 'Sea Jobs',
    'caregiver_jobs' => 'Caregiver Jobs',
    'application_tips' => 'Application Tips',
    'guides' => 'Guides',
];

if (isset($_GET['delete']) && $_SESSION['admin_role'] === 'super_admin') {
    $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL . '/admin/blog.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim(strip_tags($_POST['title'] ?? ''));
    $slugSource = trim($_POST['slug'] ?? '') ?: $title;
    $slug = buildUniqueValue($db, 'blog_posts', 'slug', slugify($slugSource), $id);
    $excerpt = trim(strip_tags($_POST['excerpt'] ?? ''));
    $content = trim(str_replace(["\r\n", "\r"], "\n", $_POST['content'] ?? ''));
    $category = $_POST['category'] ?? 'guides';
    if (!isset($allowedCategories[$category])) {
        $category = 'guides';
    }
    $authorName = trim(strip_tags($_POST['author_name'] ?? 'Global Path Africa Team'));
    $coverIcon = trim($_POST['cover_icon'] ?? 'fas fa-newspaper');
    if (!preg_match('/^[a-z0-9\-\s]+$/i', $coverIcon)) {
        $coverIcon = 'fas fa-newspaper';
    }
    $readingTime = max(1, min(30, (int)($_POST['reading_time_minutes'] ?? 5)));
    $publishedDate = trim($_POST['published_at'] ?? '');
    $publishedAt = $publishedDate ? $publishedDate . ' 09:00:00' : date('Y-m-d H:i:s');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $payload = [
        $title,
        $slug,
        $excerpt,
        $content,
        $category,
        $authorName,
        $coverIcon,
        $readingTime,
        $isFeatured,
        $isActive,
        $publishedAt,
        $_SESSION['admin_id'],
    ];

    if ($id > 0) {
        $db->prepare("
            UPDATE blog_posts
            SET title = ?, slug = ?, excerpt = ?, content = ?, category = ?, author_name = ?,
                cover_icon = ?, reading_time_minutes = ?, is_featured = ?, is_active = ?,
                published_at = ?, created_by = ?
            WHERE id = ?
        ")->execute(array_merge($payload, [$id]));
    } else {
        $db->prepare("
            INSERT INTO blog_posts (
                title, slug, excerpt, content, category, author_name, cover_icon,
                reading_time_minutes, is_featured, is_active, published_at, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute($payload);
    }

    redirect(SITE_URL . '/admin/blog.php?saved=1');
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$search = sanitize($_GET['search'] ?? '');
$categoryFilter = sanitize($_GET['category'] ?? '');
$where = ['1 = 1'];
$params = [];

if ($search) {
    $where[] = '(title LIKE ? OR excerpt LIKE ?)';
    $needle = '%' . $search . '%';
    $params[] = $needle;
    $params[] = $needle;
}

if ($categoryFilter && isset($allowedCategories[$categoryFilter])) {
    $where[] = 'category = ?';
    $params[] = $categoryFilter;
}

$sqlWhere = implode(' AND ', $where);
$totalStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE {$sqlWhere}");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();

$listStmt = $db->prepare("
    SELECT *
    FROM blog_posts
    WHERE {$sqlWhere}
    ORDER BY COALESCE(published_at, created_at) DESC, id DESC
");
$listStmt->execute($params);
$posts = $listStmt->fetchAll();
?>

<?php if ($edit || isset($_GET['add'])): ?>
<div class="admin-card">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1.5rem;"><?= $edit ? 'Edit Article' : 'Add Article' ?></h3>
    <form method="POST">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Slug</label><input type="text" name="slug" placeholder="auto-generated if left empty" value="<?= htmlspecialchars($edit['slug'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Excerpt *</label><textarea name="excerpt" required><?= htmlspecialchars($edit['excerpt'] ?? '') ?></textarea></div>
        <div class="form-group"><label class="form-label">Content *</label><textarea name="content" required style="min-height:220px;"><?= htmlspecialchars($edit['content'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category">
                    <?php foreach($allowedCategories as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= ($edit['category'] ?? 'guides') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">Author</label><input type="text" name="author_name" value="<?= htmlspecialchars($edit['author_name'] ?? 'Global Path Africa Team') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Cover Icon</label><input type="text" name="cover_icon" placeholder="e.g. fas fa-anchor" value="<?= htmlspecialchars($edit['cover_icon'] ?? 'fas fa-newspaper') ?>"></div>
            <div class="form-group"><label class="form-label">Reading Time (minutes)</label><input type="number" name="reading_time_minutes" min="1" max="30" value="<?= (int)($edit['reading_time_minutes'] ?? 5) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Publish Date</label><input type="date" name="published_at" value="<?= !empty($edit['published_at']) ? date('Y-m-d', strtotime($edit['published_at'])) : date('Y-m-d') ?>"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:1.25rem;padding-top:1.5rem;">
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_featured" <?= ($edit['is_featured'] ?? 0) ? 'checked' : '' ?>> Featured</label>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
            </div>
        </div>
        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary"><?= $edit ? '<i class="fas fa-save"></i> Update' : '<i class="fas fa-plus"></i> Add' ?> Article</button>
            <a href="blog.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Articles (<?= $total ?>)</h3>
        <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Article</a>
    </div>
    <form method="GET" class="search-row">
        <input type="text" name="search" placeholder="Search blog..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach($allowedCategories as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= $categoryFilter === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <?php if($search || $categoryFilter): ?><a href="blog.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
    <table>
        <thead><tr><th>Title</th><th>Category</th><th>Published</th><th>Flags</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($posts as $post): ?>
        <tr>
            <td>
                <strong><?= htmlspecialchars(strlen($post['title']) > 60 ? substr($post['title'], 0, 60) . '...' : $post['title']) ?></strong>
                <br><small style="color:#6b7280"><?= htmlspecialchars($post['slug']) ?></small>
            </td>
            <td><span class="badge badge-blue"><?= htmlspecialchars(blogCategoryLabel($post['category'])) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y', strtotime($post['published_at'] ?: $post['created_at'])) ?></td>
            <td>
                <?php if($post['is_featured']): ?><span class="badge badge-gold" style="margin:.1rem;">Featured</span><?php endif; ?>
                <?php if(!$post['is_active']): ?><span class="badge badge-gray" style="margin:.1rem;">Hidden</span><?php endif; ?>
            </td>
            <td>
                <div style="display:flex;gap:.4rem;">
                    <a href="?edit=<?= $post['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="<?= SITE_URL ?>/blog-detail.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="btn-icon edit" style="background:#e0e7ff;color:#4338ca" title="View"><i class="fas fa-eye"></i></a>
                    <button onclick="confirmDelete('?delete=<?= $post['id'] ?>','Delete this article?')" class="btn-icon delete" type="button"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
