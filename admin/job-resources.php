<?php
$adminTitle = 'Manage Job Resources';
require_once '../includes/content_bootstrap.php';
require_once 'header.php';

$db = getDB();
bootSiteContent($db);

$allowedCategories = [
    'sea_jobs' => 'Sea Jobs',
    'caregiver_jobs' => 'Caregiver Jobs',
    'licensing' => 'Licensing',
    'general_jobs' => 'General Jobs',
];

$allowedResourceTypes = [
    'official_employer' => 'Official Employer Portal',
    'official_government' => 'Official Government Employer',
    'official_regulator' => 'Official Regulator',
];

$allowedCostTypes = [
    'free' => 'Free Application',
    'paid' => 'Paid Requirement',
    'mixed' => 'Mixed Costs',
];

if (isset($_GET['delete']) && $_SESSION['admin_role'] === 'super_admin') {
    $db->prepare("DELETE FROM job_resources WHERE id = ?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL . '/admin/job-resources.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim(strip_tags($_POST['title'] ?? ''));
    $organization = trim(strip_tags($_POST['organization'] ?? ''));
    $resourceKeyBase = slugify(trim($_POST['resource_key'] ?? '') ?: ($organization . '-' . $title));
    $resourceKey = buildUniqueValue($db, 'job_resources', 'resource_key', $resourceKeyBase, $id);
    $category = $_POST['category'] ?? 'general_jobs';
    if (!isset($allowedCategories[$category])) {
        $category = 'general_jobs';
    }
    $region = trim(strip_tags($_POST['region'] ?? 'Global'));
    $country = trim(strip_tags($_POST['country'] ?? ''));
    $resourceType = $_POST['resource_type'] ?? 'official_employer';
    if (!isset($allowedResourceTypes[$resourceType])) {
        $resourceType = 'official_employer';
    }
    $summary = trim(strip_tags($_POST['summary'] ?? ''));
    $applyUrl = trim($_POST['apply_url'] ?? '');
    $applicationCostType = $_POST['application_cost_type'] ?? 'free';
    if (!isset($allowedCostTypes[$applicationCostType])) {
        $applicationCostType = 'free';
    }
    $costNotes = trim(strip_tags($_POST['cost_notes'] ?? ''));
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder = max(0, min(999, (int)($_POST['sort_order'] ?? 0)));

    $payload = [
        $resourceKey,
        $title,
        $organization,
        $category,
        $region,
        $country ?: null,
        $resourceType,
        $summary,
        $applyUrl,
        $applicationCostType,
        $costNotes,
        $isFeatured,
        $isActive,
        $sortOrder,
        $_SESSION['admin_id'],
    ];

    if ($id > 0) {
        $db->prepare("
            UPDATE job_resources
            SET resource_key = ?, title = ?, organization = ?, category = ?, region = ?, country = ?,
                resource_type = ?, summary = ?, apply_url = ?, application_cost_type = ?, cost_notes = ?,
                is_featured = ?, is_active = ?, sort_order = ?, created_by = ?
            WHERE id = ?
        ")->execute(array_merge($payload, [$id]));
    } else {
        $db->prepare("
            INSERT INTO job_resources (
                resource_key, title, organization, category, region, country, resource_type,
                summary, apply_url, application_cost_type, cost_notes, is_featured, is_active,
                sort_order, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute($payload);
    }

    redirect(SITE_URL . '/admin/job-resources.php?saved=1');
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM job_resources WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$search = sanitize($_GET['search'] ?? '');
$categoryFilter = sanitize($_GET['category'] ?? '');
$costFilter = sanitize($_GET['cost'] ?? '');
$where = ['1 = 1'];
$params = [];

if ($search) {
    $where[] = '(title LIKE ? OR organization LIKE ? OR summary LIKE ?)';
    $needle = '%' . $search . '%';
    $params[] = $needle;
    $params[] = $needle;
    $params[] = $needle;
}

if ($categoryFilter && isset($allowedCategories[$categoryFilter])) {
    $where[] = 'category = ?';
    $params[] = $categoryFilter;
}

if ($costFilter && isset($allowedCostTypes[$costFilter])) {
    $where[] = 'application_cost_type = ?';
    $params[] = $costFilter;
}

$sqlWhere = implode(' AND ', $where);
$totalStmt = $db->prepare("SELECT COUNT(*) FROM job_resources WHERE {$sqlWhere}");
$totalStmt->execute($params);
$total = $totalStmt->fetchColumn();

$listStmt = $db->prepare("
    SELECT *
    FROM job_resources
    WHERE {$sqlWhere}
    ORDER BY is_featured DESC, sort_order ASC, organization ASC
");
$listStmt->execute($params);
$resources = $listStmt->fetchAll();
?>

<?php if ($edit || isset($_GET['add'])): ?>
<div class="admin-card">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1.5rem;"><?= $edit ? 'Edit Resource' : 'Add Resource' ?></h3>
    <form method="POST">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Organisation *</label><input type="text" name="organization" required value="<?= htmlspecialchars($edit['organization'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Resource Key</label><input type="text" name="resource_key" placeholder="auto-generated if left empty" value="<?= htmlspecialchars($edit['resource_key'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-label">Application URL *</label><input type="url" name="apply_url" required value="<?= htmlspecialchars($edit['apply_url'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Summary *</label><textarea name="summary" required><?= htmlspecialchars($edit['summary'] ?? '') ?></textarea></div>
        <div class="form-group"><label class="form-label">Cost Notes *</label><textarea name="cost_notes" required><?= htmlspecialchars($edit['cost_notes'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category">
                    <?php foreach($allowedCategories as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= ($edit['category'] ?? 'general_jobs') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Resource Type</label>
                <select name="resource_type">
                    <?php foreach($allowedResourceTypes as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= ($edit['resource_type'] ?? 'official_employer') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Region</label><input type="text" name="region" value="<?= htmlspecialchars($edit['region'] ?? 'Global') ?>"></div>
            <div class="form-group"><label class="form-label">Country</label><input type="text" name="country" value="<?= htmlspecialchars($edit['country'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Cost Classification</label>
                <select name="application_cost_type">
                    <?php foreach($allowedCostTypes as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= ($edit['application_cost_type'] ?? 'free') === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" min="0" max="999" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group" style="display:flex;align-items:center;gap:1.25rem;padding-top:1.5rem;">
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_featured" <?= ($edit['is_featured'] ?? 0) ? 'checked' : '' ?>> Featured</label>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label>
            </div>
        </div>
        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary"><?= $edit ? '<i class="fas fa-save"></i> Update' : '<i class="fas fa-plus"></i> Add' ?> Resource</button>
            <a href="job-resources.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Resources (<?= $total ?>)</h3>
        <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Resource</a>
    </div>
    <form method="GET" class="search-row">
        <input type="text" name="search" placeholder="Search resources..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach($allowedCategories as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= $categoryFilter === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="cost">
            <option value="">All Cost Types</option>
            <?php foreach($allowedCostTypes as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= $costFilter === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <?php if($search || $categoryFilter || $costFilter): ?><a href="job-resources.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
    <table>
        <thead><tr><th>Title</th><th>Category</th><th>Cost</th><th>Location</th><th>Flags</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($resources as $resource): $costMeta = jobResourceCostMeta($resource['application_cost_type']); ?>
        <tr>
            <td>
                <strong><?= htmlspecialchars(strlen($resource['title']) > 52 ? substr($resource['title'], 0, 52) . '...' : $resource['title']) ?></strong>
                <br><small style="color:#6b7280"><?= htmlspecialchars($resource['organization']) ?></small>
            </td>
            <td><span class="badge badge-blue"><?= htmlspecialchars(resourceCategoryLabel($resource['category'])) ?></span></td>
            <td><span class="badge <?= $costMeta['class'] === 'warning' ? 'badge-gold' : ($costMeta['class'] === 'info' ? 'badge-blue' : 'badge-green') ?>"><?= htmlspecialchars($costMeta['label']) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= htmlspecialchars($resource['country'] ?: $resource['region']) ?></td>
            <td>
                <?php if($resource['is_featured']): ?><span class="badge badge-gold" style="margin:.1rem;">Featured</span><?php endif; ?>
                <?php if(!$resource['is_active']): ?><span class="badge badge-gray" style="margin:.1rem;">Hidden</span><?php endif; ?>
            </td>
            <td>
                <div style="display:flex;gap:.4rem;">
                    <a href="?edit=<?= $resource['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="<?= htmlspecialchars($resource['apply_url']) ?>" target="_blank" class="btn-icon edit" style="background:#e0e7ff;color:#4338ca" title="View"><i class="fas fa-eye"></i></a>
                    <button onclick="confirmDelete('?delete=<?= $resource['id'] ?>','Delete this resource?')" class="btn-icon delete" type="button"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
