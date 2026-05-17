<?php
$adminTitle = 'Payments';
require_once 'header.php';
$db = getDB();

// Approve pending bank transfer
if(isset($_GET['approve'])){
    $pid=(int)$_GET['approve'];
    $stmt=$db->prepare("SELECT * FROM payments WHERE id=?");$stmt->execute([$pid]);$p=$stmt->fetch();
    if($p && $p['status']==='pending'){
        $db->prepare("UPDATE payments SET status='completed' WHERE id=?")->execute([$pid]);
        activatePurchasedPlan($db, $p['user_id'], $p['plan']);
    }
    redirect(SITE_URL.'/admin/payments.php?saved=1');
}
if(isset($_GET['reject'])){
    $db->prepare("UPDATE payments SET status='failed' WHERE id=?")->execute([(int)$_GET['reject']]);
    redirect(SITE_URL.'/admin/payments.php?deleted=1');
}

$filter=sanitize($_GET['filter']??'');
$where=$filter?"WHERE status=?":'';
$params=$filter?[$filter]:[];
$page=max(1,(int)($_GET['page']??1));$perPage=20;$offset=($page-1)*$perPage;
$total=$db->prepare("SELECT COUNT(*) FROM payments $where");$total->execute($params);$total=$total->fetchColumn();
$list=$db->prepare("SELECT p.*,u.first_name,u.last_name,u.email FROM payments p JOIN users u ON p.user_id=u.id $where ORDER BY p.payment_date DESC LIMIT $perPage OFFSET $offset");
$list->execute($params);$payments=$list->fetchAll();
$revenue=$db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='completed'")->fetchColumn();
?>
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:1.5rem;">
    <div class="stat-box"><div class="stat-icon green"><i class="fas fa-dollar-sign"></i></div><div><div class="stat-val">$<?= number_format($revenue,0) ?></div><div class="stat-lbl">Total Revenue</div></div></div>
    <div class="stat-box"><div class="stat-icon gold"><i class="fas fa-clock"></i></div><div><div class="stat-val"><?= $db->query("SELECT COUNT(*) FROM payments WHERE status='pending'")->fetchColumn() ?></div><div class="stat-lbl">Pending</div></div></div>
    <div class="stat-box"><div class="stat-icon blue"><i class="fas fa-check-circle"></i></div><div><div class="stat-val"><?= $db->query("SELECT COUNT(*) FROM payments WHERE status='completed'")->fetchColumn() ?></div><div class="stat-lbl">Completed</div></div></div>
    <div class="stat-box"><div class="stat-icon purple"><i class="fas fa-users"></i></div><div><div class="stat-val"><?= $db->query("SELECT COUNT(*) FROM users WHERE membership_type!='free'")->fetchColumn() ?></div><div class="stat-lbl">Premium Users</div></div></div>
</div>
<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;">All Payments (<?= $total ?>)</h3>
        <div style="display:flex;gap:.5rem;">
            <?php foreach([''=>'All','pending'=>'Pending','completed'=>'Completed','failed'=>'Failed'] as $v=>$l): ?>
            <a href="?filter=<?= $v ?>" class="btn btn-sm <?= $filter===$v?'btn-primary':'btn-outline' ?>"><?= $l ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <table>
        <thead><tr><th>Member</th><th>Plan</th><th>Amount</th><th>Gateway</th><th>Transaction ID</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($payments as $p): ?>
        <tr>
            <td><strong><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></strong><br><small style="color:#6b7280"><?= htmlspecialchars($p['email']) ?></small></td>
            <td style="font-size:.8rem"><?= htmlspecialchars(formatPlanName($p['plan'])) ?></td>
            <td style="font-weight:600"><?= formatCurrency($p['amount'],$p['currency']) ?></td>
            <td style="text-transform:capitalize;font-size:.82rem"><?= htmlspecialchars($p['gateway']) ?></td>
            <td style="font-size:.75rem;font-family:monospace;color:#6b7280"><?= htmlspecialchars(substr($p['transaction_id'],0,22)) ?>...</td>
            <td><span class="badge badge-<?= $p['status']==='completed'?'green':($p['status']==='pending'?'gold':'red') ?>"><?= ucfirst($p['status']) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y',strtotime($p['payment_date'])) ?></td>
            <td>
                <?php if($p['status']==='pending'): ?>
                <div style="display:flex;gap:.35rem;">
                    <a href="?approve=<?= $p['id'] ?>" class="btn-icon edit" style="background:#dcfce7;color:#166534" onclick="return confirm('Approve and activate membership?')" title="Approve"><i class="fas fa-check"></i></a>
                    <a href="?reject=<?= $p['id'] ?>" class="btn-icon delete" onclick="return confirm('Reject this payment?')" title="Reject"><i class="fas fa-times"></i></a>
                </div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
