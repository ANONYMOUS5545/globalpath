<?php
$adminTitle = 'Applications';
require_once 'header.php';
$db = getDB();

// Update status
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])){
    $db->prepare("UPDATE applications SET status=?,admin_notes=? WHERE id=?")
       ->execute([sanitize($_POST['status']??''),sanitize($_POST['admin_notes']??''),(int)$_POST['app_id']]);
    redirect(SITE_URL.'/admin/applications.php?saved=1');
}

$filter=sanitize($_GET['filter']??'');
$type=sanitize($_GET['type']??'');
$where=['1=1'];$params=[];
if($filter){ $where[]='a.status=?'; $params[]=$filter; }
if($type){ $where[]='a.type=?'; $params[]=$type; }
$whereSQL='WHERE '.implode(' AND ',$where);
$page=max(1,(int)($_GET['page']??1));$perPage=20;$offset=($page-1)*$perPage;
$total=$db->prepare("SELECT COUNT(*) FROM applications a $whereSQL");$total->execute($params);$total=$total->fetchColumn();
$list=$db->prepare("SELECT a.*,u.first_name,u.last_name,u.email FROM applications a JOIN users u ON a.user_id=u.id $whereSQL ORDER BY a.created_at DESC LIMIT $perPage OFFSET $offset");
$list->execute($params);$apps=$list->fetchAll();

$editApp=null;
if(isset($_GET['update'])){ $s=$db->prepare("SELECT * FROM applications WHERE id=?");$s->execute([(int)$_GET['update']]);$editApp=$s->fetch(); }
?>

<?php if($editApp): ?>
<div class="admin-card" style="max-width:500px;">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Update Application #<?= $editApp['id'] ?></h3>
    <form method="POST">
        <input type="hidden" name="update_status" value="1">
        <input type="hidden" name="app_id" value="<?= $editApp['id'] ?>">
        <div class="form-group"><label class="form-label">Status</label>
            <select name="status">
                <?php foreach(['submitted','under_review','accepted','rejected','withdrawn'] as $s): ?>
                <option value="<?=$s?>" <?=$editApp['status']===$s?'selected':''?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label class="form-label">Admin Notes</label><textarea name="admin_notes"><?= htmlspecialchars($editApp['admin_notes']??'') ?></textarea></div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Update</button>
            <a href="applications.php" class="btn btn-outline btn-sm">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Applications (<?= $total ?>)</h3>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <?php foreach([''=>'All','submitted'=>'Submitted','under_review'=>'Under Review','accepted'=>'Accepted','rejected'=>'Rejected'] as $v=>$l): ?>
            <a href="?filter=<?= $v ?>&type=<?= $type ?>" class="btn btn-sm <?= $filter===$v?'btn-primary':'btn-outline' ?>"><?= $l ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <table>
        <thead><tr><th>Member</th><th>Type</th><th>Ref ID</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($apps as $a):
            $c=['submitted'=>'blue','under_review'=>'purple','accepted'=>'green','rejected'=>'red','withdrawn'=>'gray'][$a['status']]??'gray';
        ?>
        <tr>
            <td><strong><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></strong><br><small style="color:#6b7280"><?= htmlspecialchars($a['email']) ?></small></td>
            <td><span class="badge badge-blue"><?= ucfirst($a['type']) ?></span></td>
            <td>#<?= $a['reference_id'] ?></td>
            <td><span class="badge badge-<?= $c ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y',strtotime($a['created_at'])) ?></td>
            <td><a href="?update=<?= $a['id'] ?>" class="btn-icon edit" title="Update Status"><i class="fas fa-edit"></i></a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
