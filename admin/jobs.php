<?php
$adminTitle = 'Manage Jobs';
require_once '../includes/content_bootstrap.php';
require_once 'header.php';
$db = getDB();
bootSiteContent($db);

if(isset($_GET['delete']) && $_SESSION['admin_role']==='super_admin'){
    $db->prepare("DELETE FROM jobs WHERE id=?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL.'/admin/jobs.php?deleted=1');
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0);
    $f=[
        sanitize($_POST['title']??''),sanitize($_POST['organization']??''),
        sanitize($_POST['location']??''),sanitize($_POST['country']??''),
        htmlspecialchars_decode(strip_tags($_POST['description']??'')),
        htmlspecialchars_decode(strip_tags($_POST['requirements']??'')),
        sanitize($_POST['salary_range']??''),
        $_POST['deadline']??null,
        sanitize($_POST['link']??''),sanitize($_POST['source_org']??''),
        sanitize($_POST['job_type']??'full_time'),sanitize($_POST['sector']??''),
        in_array($_POST['access_tier'] ?? 'free', ['free','premium','premium_plus'], true) ? $_POST['access_tier'] : 'free',
        isset($_POST['is_featured'])?1:0,
        isset($_POST['is_active'])?1:0,
        $_SESSION['admin_id'],
    ];
    if($id){
        $db->prepare("UPDATE jobs SET title=?,organization=?,location=?,country=?,description=?,requirements=?,salary_range=?,deadline=?,link=?,source_org=?,job_type=?,sector=?,access_tier=?,is_featured=?,is_active=?,created_by=? WHERE id=?")->execute(array_merge($f,[$id]));
    } else {
        $db->prepare("INSERT INTO jobs (title,organization,location,country,description,requirements,salary_range,deadline,link,source_org,job_type,sector,access_tier,is_featured,is_active,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($f);
    }
    redirect(SITE_URL.'/admin/jobs.php?saved=1');
}

$edit=null;
if(isset($_GET['edit'])){ $s=$db->prepare("SELECT * FROM jobs WHERE id=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch(); }

$search=sanitize($_GET['search']??'');
$where=$search?"WHERE title LIKE ? OR organization LIKE ?":'';
$params=$search?["%$search%","%$search%"]:[];
$page=max(1,(int)($_GET['page']??1));$perPage=15;$offset=($page-1)*$perPage;
$total=$db->prepare("SELECT COUNT(*) FROM jobs $where");$total->execute($params);$total=$total->fetchColumn();
$list=$db->prepare("SELECT * FROM jobs $where ORDER BY " . getJobOrderBySql() . " LIMIT $perPage OFFSET $offset");
$list->execute($params);$jobs=$list->fetchAll();
?>

<?php if($edit||isset($_GET['add'])): ?>
<div class="admin-card">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1.5rem;"><?= $edit?'Edit Job':'Add New Job' ?></h3>
    <form method="POST">
        <?php if($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Job Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Organisation *</label><input type="text" name="organization" required value="<?= htmlspecialchars($edit['organization']??'') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Location</label><input type="text" name="location" value="<?= htmlspecialchars($edit['location']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Country *</label><input type="text" name="country" required value="<?= htmlspecialchars($edit['country']??'') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Description *</label><textarea name="description" required><?= htmlspecialchars($edit['description']??'') ?></textarea></div>
        <div class="form-group"><label class="form-label">Requirements</label><textarea name="requirements"><?= htmlspecialchars($edit['requirements']??'') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Salary Range</label><input type="text" name="salary_range" placeholder="e.g. $50,000 – $70,000" value="<?= htmlspecialchars($edit['salary_range']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Sector</label><input type="text" name="sector" value="<?= htmlspecialchars($edit['sector']??'') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Application Link</label><input type="url" name="link" value="<?= htmlspecialchars($edit['link']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Deadline</label><input type="date" name="deadline" value="<?= $edit['deadline']??'' ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Job Type</label>
                <select name="job_type">
                    <?php foreach(['full_time'=>'Full Time','part_time'=>'Part Time','contract'=>'Contract','internship'=>'Internship','volunteer'=>'Volunteer'] as $v=>$l): ?>
                    <option value="<?=$v?>" <?=($edit['job_type']??'')===$v?'selected':''?>><?=$l?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">Access Tier</label>
                <select name="access_tier">
                    <?php foreach(['free'=>'Free Plan','premium'=>'Premium Listing','premium_plus'=>'Premium Plus Listing'] as $v=>$l): ?>
                    <option value="<?=$v?>" <?= (($edit['access_tier'] ?? (($edit['is_premium_only'] ?? 0) ? 'premium' : 'free')) === $v) ? 'selected' : '' ?>><?=$l?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group" style="display:flex;align-items:center;gap:1.25rem;padding-top:1.5rem;">
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_featured" <?=($edit['is_featured']??0)?'checked':''?>> Featured</label>
                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer;"><input type="checkbox" name="is_active" <?=($edit['is_active']??1)?'checked':''?>> Active</label>
            </div>
        </div>
        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary"><?= $edit?'<i class="fas fa-save"></i> Update':'<i class="fas fa-plus"></i> Add' ?> Job</button>
            <a href="jobs.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Jobs (<?= $total ?>)</h3>
        <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Job</a>
    </div>
    <form method="GET" class="search-row">
        <input type="text" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <?php if($search): ?><a href="jobs.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
    <table>
        <thead><tr><th>Title</th><th>Organisation</th><th>Country</th><th>Type</th><th>Deadline</th><th>Tier</th><th>Flags</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($jobs as $j): ?>
        <tr>
            <td><strong><?= htmlspecialchars(substr($j['title'],0,45)) ?>...</strong></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($j['organization']) ?></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($j['country']) ?></td>
            <td><span class="badge badge-blue"><?= str_replace('_',' ',ucfirst($j['job_type'])) ?></span></td>
            <?php $deadlineMeta = jobDeadlineMeta($j); $jobTier = $j['access_tier'] ?? (($j['is_premium_only'] ?? 0) ? 'premium' : 'free'); ?>
            <td style="font-size:.8rem;color:#6b7280"><?= htmlspecialchars($deadlineMeta['label']) ?></td>
            <td><span class="badge <?= jobAccessTierBadgeClass($jobTier) ?>"><?= htmlspecialchars(jobAccessTierLabel($jobTier)) ?></span></td>
            <td>
                <?php if($j['is_premium_only']): ?><span class="badge badge-gold" style="margin:.1rem;">⭐ PP</span><?php endif; ?>
                <?php if($j['is_featured']): ?><span class="badge badge-green" style="margin:.1rem;">Featured</span><?php endif; ?>
                <?php if(!$j['is_active']): ?><span class="badge badge-gray" style="margin:.1rem;">Hidden</span><?php endif; ?>
            </td>
            <td>
                <div style="display:flex;gap:.4rem;">
                    <a href="?edit=<?= $j['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="<?= SITE_URL ?>/job-detail.php?id=<?= $j['id'] ?>" target="_blank" class="btn-icon edit" style="background:#e0e7ff;color:#4338ca" title="View"><i class="fas fa-eye"></i></a>
                    <button onclick="confirmDelete('?delete=<?= $j['id'] ?>','Delete this job?')" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
