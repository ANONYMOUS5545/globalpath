<?php
$adminTitle = 'Manage Members';
require_once 'header.php';
$db = getDB();

// Activate membership manually
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['activate'])){
    $uid=(int)$_POST['user_id'];
    $type=sanitize($_POST['membership_type']);
    $months=(int)($_POST['months']??1);
    $exp=date('Y-m-d H:i:s',strtotime("+{$months} months"));
    $db->prepare("UPDATE users SET membership_type=?,membership_expires=? WHERE id=?")->execute([$type,$exp,$uid]);
    redirect(SITE_URL.'/admin/users.php?saved=1');
}
if(isset($_GET['suspend'])){
    $db->prepare("UPDATE users SET status='suspended' WHERE id=?")->execute([(int)$_GET['suspend']]);
    redirect(SITE_URL.'/admin/users.php?saved=1');
}
if(isset($_GET['activate_user'])){
    $db->prepare("UPDATE users SET status='active' WHERE id=?")->execute([(int)$_GET['activate_user']]);
    redirect(SITE_URL.'/admin/users.php?saved=1');
}

$search=sanitize($_GET['search']??'');
$filter=sanitize($_GET['filter']??'');
$where=['1=1'];$params=[];
if($search){ $where[]="(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)"; $params=array_merge($params,["%$search%","%$search%","%$search%"]); }
if($filter){ $where[]="membership_type=?"; $params[]=$filter; }
$whereSQL='WHERE '.implode(' AND ',$where);
$page=max(1,(int)($_GET['page']??1));$perPage=20;$offset=($page-1)*$perPage;
$total=$db->prepare("SELECT COUNT(*) FROM users $whereSQL");$total->execute($params);$total=$total->fetchColumn();
$list=$db->prepare("SELECT * FROM users $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$list->execute($params);$users=$list->fetchAll();

$viewUser=null;
if(isset($_GET['view'])){ $s=$db->prepare("SELECT * FROM users WHERE id=?");$s->execute([(int)$_GET['view']]);$viewUser=$s->fetch(); }
?>

<?php if($viewUser): ?>
<div class="admin-card" style="max-width:600px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;">Member Profile</h3>
        <a href="users.php" class="btn btn-outline btn-sm">← Back</a>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;font-size:.875rem;margin-bottom:1.5rem;">
        <div><span style="color:#6b7280">Name:</span> <strong><?= htmlspecialchars($viewUser['first_name'].' '.$viewUser['last_name']) ?></strong></div>
        <div><span style="color:#6b7280">Email:</span> <?= htmlspecialchars($viewUser['email']) ?></div>
        <div><span style="color:#6b7280">Country:</span> <?= htmlspecialchars($viewUser['country']??'—') ?></div>
        <div><span style="color:#6b7280">Phone:</span> <?= htmlspecialchars($viewUser['phone']??'—') ?></div>
        <div><span style="color:#6b7280">Membership:</span> <span class="badge badge-<?= $viewUser['membership_type']==='free'?'gray':'green' ?>"><?= ucfirst(str_replace('_',' ',$viewUser['membership_type'])) ?></span></div>
        <div><span style="color:#6b7280">Expires:</span> <?= $viewUser['membership_expires']?date('d M Y',strtotime($viewUser['membership_expires'])):'—' ?></div>
        <div><span style="color:#6b7280">Status:</span> <span class="badge badge-<?= $viewUser['status']==='active'?'green':'red' ?>"><?= ucfirst($viewUser['status']) ?></span></div>
        <div><span style="color:#6b7280">Joined:</span> <?= date('d M Y',strtotime($viewUser['created_at'])) ?></div>
    </div>
    <form method="POST" style="border-top:1px solid #e5e7eb;padding-top:1.25rem;">
        <input type="hidden" name="activate" value="1">
        <input type="hidden" name="user_id" value="<?= $viewUser['id'] ?>">
        <h4 style="font-size:.9rem;font-weight:600;margin-bottom:.75rem;">Manually Activate Membership</h4>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="margin:0;flex:1;">
                <label class="form-label">Plan</label>
                <select name="membership_type" style="padding:.5rem .75rem;">
                    <option value="premium">Premium</option>
                    <option value="premium_plus">Premium Plus</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;flex:1;">
                <label class="form-label">Duration (months)</label>
                <select name="months" style="padding:.5rem .75rem;">
                    <?php foreach([1,3,6,12] as $m): ?><option value="<?=$m?>"><?=$m?> month<?=$m>1?'s':''?></option><?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Activate</button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Members (<?= $total ?>)</h3>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <?php foreach([''=>'All','free'=>'Free','premium'=>'Premium','premium_plus'=>'Premium Plus'] as $v=>$l): ?>
            <a href="?filter=<?= $v ?>" class="btn btn-sm <?= $filter===$v?'btn-primary':'btn-outline' ?>"><?= $l ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <form method="GET" class="search-row">
        <input type="text" name="search" placeholder="Search name or email..." value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
    </form>
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Country</th><th>Plan</th><th>Expires</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($users as $u): ?>
        <tr>
            <td><strong><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></strong></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($u['email']) ?></td>
            <td style="font-size:.82rem"><?= htmlspecialchars($u['country']??'—') ?></td>
            <td><span class="badge badge-<?= $u['membership_type']==='free'?'gray':($u['membership_type']==='premium_plus'?'gold':'green') ?>"><?= ucfirst(str_replace('_',' ',$u['membership_type'])) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= $u['membership_expires']?date('d M Y',strtotime($u['membership_expires'])):'—' ?></td>
            <td><span class="badge badge-<?= $u['status']==='active'?'green':'red' ?>"><?= ucfirst($u['status']) ?></span></td>
            <td>
                <div style="display:flex;gap:.35rem;">
                    <a href="?view=<?= $u['id'] ?>" class="btn-icon edit" title="View/Activate"><i class="fas fa-user-cog"></i></a>
                    <?php if($u['status']==='active'): ?>
                    <a href="?suspend=<?= $u['id'] ?>" class="btn-icon delete" onclick="return confirm('Suspend this user?')" title="Suspend"><i class="fas fa-ban"></i></a>
                    <?php else: ?>
                    <a href="?activate_user=<?= $u['id'] ?>" class="btn-icon edit" style="background:#dcfce7;color:#166534" title="Activate"><i class="fas fa-check"></i></a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php $totalPages=ceil($total/$perPage); if($totalPages>1): ?>
    <div class="pagination">
        <?php for($i=1;$i<=$totalPages;$i++): ?>
        <a href="?page=<?=$i?>&search=<?=urlencode($search)?>&filter=<?= $filter ?>" class="page-btn <?=$i==$page?'active':''?>"><?=$i?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
