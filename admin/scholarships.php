<?php
$adminTitle = 'Manage Scholarships';
require_once 'header.php';
$db = getDB();
$msg = '';

// Handle delete
if(isset($_GET['delete']) && $_SESSION['admin_role']==='super_admin'){
    $db->prepare("DELETE FROM scholarships WHERE id=?")->execute([(int)$_GET['delete']]);
    redirect(SITE_URL.'/admin/scholarships.php?deleted=1');
}

// Handle add/edit
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0);
    $fields=[
        sanitize($_POST['title']??''),
        sanitize($_POST['provider']??''),
        sanitize($_POST['country']??''),
        htmlspecialchars_decode(strip_tags($_POST['description']??'')),
        htmlspecialchars_decode(strip_tags($_POST['eligibility']??'')),
        htmlspecialchars_decode(strip_tags($_POST['benefits']??'')),
        $_POST['deadline']??null,
        sanitize($_POST['link']??''),
        sanitize($_POST['source_org']??''),
        sanitize($_POST['field_of_study']??''),
        sanitize($_POST['level']??'all'),
        sanitize($_POST['type']??'full'),
        isset($_POST['is_featured'])?1:0,
        isset($_POST['is_active'])?1:0,
        $_SESSION['admin_id'],
    ];
    if($id){
        $db->prepare("UPDATE scholarships SET title=?,provider=?,country=?,description=?,eligibility=?,benefits=?,deadline=?,link=?,source_org=?,field_of_study=?,level=?,type=?,is_featured=?,is_active=?,created_by=? WHERE id=?")
           ->execute(array_merge($fields,[$id]));
    } else {
        $db->prepare("INSERT INTO scholarships (title,provider,country,description,eligibility,benefits,deadline,link,source_org,field_of_study,level,type,is_featured,is_active,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
           ->execute($fields);
    }
    redirect(SITE_URL.'/admin/scholarships.php?saved=1');
}

// Edit mode
$edit = null;
if(isset($_GET['edit'])){
    $stmt=$db->prepare("SELECT * FROM scholarships WHERE id=?"); $stmt->execute([(int)$_GET['edit']]); $edit=$stmt->fetch();
}

// Listing
$search=sanitize($_GET['search']??'');
$where=$search?"WHERE title LIKE ? OR provider LIKE ?":'';
$params=$search?["%$search%","%$search%"]:[];
$page=max(1,(int)($_GET['page']??1));$perPage=15;$offset=($page-1)*$perPage;
$total=$db->prepare("SELECT COUNT(*) FROM scholarships $where");$total->execute($params);$total=$total->fetchColumn();
$list=$db->prepare("SELECT * FROM scholarships $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$list->execute($params);$scholarships=$list->fetchAll();
?>

<?php if($edit||isset($_GET['add'])): ?>
<div class="admin-card">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1.5rem;"><?= $edit?'Edit Scholarship':'Add New Scholarship' ?></h3>
    <form method="POST">
        <?php if($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Provider *</label><input type="text" name="provider" required value="<?= htmlspecialchars($edit['provider']??'') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Host Country *</label><input type="text" name="country" required value="<?= htmlspecialchars($edit['country']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Source Organisation</label><input type="text" name="source_org" value="<?= htmlspecialchars($edit['source_org']??'') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">Description *</label><textarea name="description" required><?= htmlspecialchars($edit['description']??'') ?></textarea></div>
        <div class="form-group"><label class="form-label">Eligibility</label><textarea name="eligibility"><?= htmlspecialchars($edit['eligibility']??'') ?></textarea></div>
        <div class="form-group"><label class="form-label">Benefits / Funding Details</label><textarea name="benefits"><?= htmlspecialchars($edit['benefits']??'') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Field of Study</label><input type="text" name="field_of_study" value="<?= htmlspecialchars($edit['field_of_study']??'') ?>"></div>
            <div class="form-group"><label class="form-label">Official Link</label><input type="url" name="link" value="<?= htmlspecialchars($edit['link']??'') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Deadline</label><input type="date" name="deadline" value="<?= $edit['deadline']??'' ?>"></div>
            <div class="form-group"><label class="form-label">Level</label>
                <select name="level">
                    <?php foreach(['all'=>'All Levels','undergraduate'=>'Undergraduate','postgraduate'=>'Postgraduate','phd'=>'PhD'] as $v=>$l): ?>
                    <option value="<?=$v?>" <?=($edit['level']??'')===$v?'selected':''?>><?=$l?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Type</label>
                <select name="type">
                    <?php foreach(['full'=>'Fully Funded','partial'=>'Partial','fellowship'=>'Fellowship','exchange'=>'Exchange'] as $v=>$l): ?>
                    <option value="<?=$v?>" <?=($edit['type']??'')===$v?'selected':''?>><?=$l?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:1.5rem;padding-top:1.5rem;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;"><input type="checkbox" name="is_featured" <?=($edit['is_featured']??0)?'checked':''?>> Featured</label>
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;"><input type="checkbox" name="is_active" <?=($edit['is_active']??1)?'checked':''?>> Active</label>
            </div>
        </div>
        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn btn-primary"><?= $edit?'<i class="fas fa-save"></i> Update':'<i class="fas fa-plus"></i> Add' ?> Scholarship</button>
            <a href="scholarships.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Scholarships (<?= $total ?>)</h3>
        <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Scholarship</a>
    </div>
    <form method="GET" class="search-row">
        <input type="text" name="search" placeholder="Search scholarships..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <?php if($search): ?><a href="scholarships.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
    <table>
        <thead><tr><th>Title</th><th>Provider</th><th>Country</th><th>Level</th><th>Deadline</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($scholarships as $s): ?>
        <tr>
            <td><strong><?= htmlspecialchars(substr($s['title'],0,50)) ?><?= strlen($s['title'])>50?'...':'' ?></strong></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($s['provider']) ?></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($s['country']) ?></td>
            <td><span class="badge badge-blue"><?= ucfirst($s['level']) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= $s['deadline']?date('d M Y',strtotime($s['deadline'])):'Open' ?></td>
            <td>
                <?php if($s['is_featured']): ?><span class="badge badge-gold">Featured</span><?php endif; ?>
                <span class="badge badge-<?= $s['is_active']?'green':'gray' ?>"><?= $s['is_active']?'Active':'Hidden' ?></span>
            </td>
            <td>
                <div style="display:flex;gap:.4rem;">
                    <a href="?edit=<?= $s['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-edit"></i></a>
                    <a href="<?= SITE_URL ?>/scholarship-detail.php?id=<?= $s['id'] ?>" target="_blank" class="btn-icon edit" title="View" style="background:#e0e7ff;color:#4338ca"><i class="fas fa-eye"></i></a>
                    <button onclick="confirmDelete('?delete=<?= $s['id'] ?>','Delete this scholarship?')" class="btn-icon delete" title="Delete"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(ceil($total/15)>1): ?>
    <div class="pagination">
        <?php for($i=1;$i<=ceil($total/15);$i++): ?>
        <a href="?page=<?=$i?>&search=<?=urlencode($search)?>" class="page-btn <?=$i==$page?'active':''?>"><?=$i?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
