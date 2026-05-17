<?php
$adminTitle = 'Dashboard';
require_once 'header.php';
$db = getDB();

$stats = [
    'users'       => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'premium'     => $db->query("SELECT COUNT(*) FROM users WHERE membership_type!='free'")->fetchColumn(),
    'scholarships'=> $db->query("SELECT COUNT(*) FROM scholarships WHERE is_active=1")->fetchColumn(),
    'jobs'        => $db->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetchColumn(),
    'applications'=> $db->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
    'revenue'     => $db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='completed'")->fetchColumn(),
    'pending_pay' => $db->query("SELECT COUNT(*) FROM payments WHERE status='pending'")->fetchColumn(),
    'messages'    => $db->query("SELECT COUNT(*) FROM support_messages WHERE status='open'")->fetchColumn(),
];

$recentUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 8")->fetchAll();
$recentPayments = $db->query("SELECT p.*,u.first_name,u.last_name,u.email FROM payments p JOIN users u ON p.user_id=u.id ORDER BY p.payment_date DESC LIMIT 8")->fetchAll();
$recentApps = $db->query("SELECT a.*,u.first_name,u.last_name FROM applications a JOIN users u ON a.user_id=u.id ORDER BY a.created_at DESC LIMIT 6")->fetchAll();
?>

<div class="stat-grid">
    <div class="stat-box"><div class="stat-icon green"><i class="fas fa-users"></i></div><div><div class="stat-val"><?= number_format($stats['users']) ?></div><div class="stat-lbl">Total Members</div></div></div>
    <div class="stat-box"><div class="stat-icon gold"><i class="fas fa-star"></i></div><div><div class="stat-val"><?= number_format($stats['premium']) ?></div><div class="stat-lbl">Premium Members</div></div></div>
    <div class="stat-box"><div class="stat-icon blue"><i class="fas fa-graduation-cap"></i></div><div><div class="stat-val"><?= number_format($stats['scholarships']) ?></div><div class="stat-lbl">Scholarships</div></div></div>
    <div class="stat-box"><div class="stat-icon purple"><i class="fas fa-briefcase"></i></div><div><div class="stat-val"><?= number_format($stats['jobs']) ?></div><div class="stat-lbl">Active Jobs</div></div></div>
    <div class="stat-box"><div class="stat-icon green"><i class="fas fa-file-alt"></i></div><div><div class="stat-val"><?= number_format($stats['applications']) ?></div><div class="stat-lbl">Applications</div></div></div>
    <div class="stat-box"><div class="stat-icon gold"><i class="fas fa-dollar-sign"></i></div><div><div class="stat-val">$<?= number_format($stats['revenue'],0) ?></div><div class="stat-lbl">Total Revenue</div></div></div>
    <div class="stat-box"><div class="stat-icon blue"><i class="fas fa-clock"></i></div><div><div class="stat-val"><?= number_format($stats['pending_pay']) ?></div><div class="stat-lbl">Pending Payments</div></div></div>
    <div class="stat-box"><div class="stat-icon purple"><i class="fas fa-comments"></i></div><div><div class="stat-val"><?= number_format($stats['messages']) ?></div><div class="stat-lbl">Open Messages</div></div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <div class="admin-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h3 style="font-size:1rem;font-weight:700;">Recent Members</h3>
            <a href="users.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <table>
            <thead><tr><th>Name</th><th>Country</th><th>Plan</th><th>Joined</th></tr></thead>
            <tbody>
            <?php foreach($recentUsers as $u): ?>
            <tr>
                <td><strong><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></strong><br><small style="color:#6b7280"><?= htmlspecialchars($u['email']) ?></small></td>
                <td style="font-size:.8rem"><?= htmlspecialchars($u['country']??'—') ?></td>
                <td><span class="badge badge-<?= $u['membership_type']==='free'?'gray':($u['membership_type']==='premium_plus'?'gold':'green') ?>"><?= ucfirst(str_replace('_',' ',$u['membership_type'])) ?></span></td>
                <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y',strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h3 style="font-size:1rem;font-weight:700;">Recent Payments</h3>
            <a href="payments.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <table>
            <thead><tr><th>Member</th><th>Plan</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach($recentPayments as $p): ?>
            <tr>
                <td style="font-size:.82rem"><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></td>
                <td style="font-size:.78rem;color:#6b7280"><?= str_replace('_',' ',ucfirst($p['plan'])) ?></td>
                <td style="font-weight:600">$<?= number_format($p['amount'],2) ?></td>
                <td><span class="badge badge-<?= $p['status']==='completed'?'green':($p['status']==='pending'?'gold':'red') ?>"><?= ucfirst($p['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h3 style="font-size:1rem;font-weight:700;">Recent Applications</h3>
        <a href="applications.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <table>
        <thead><tr><th>Member</th><th>Type</th><th>Ref ID</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($recentApps as $a):
            $c=['submitted'=>'blue','under_review'=>'purple','accepted'=>'green','rejected'=>'red','withdrawn'=>'gray'][$a['status']]??'gray';
        ?>
        <tr>
            <td><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></td>
            <td><span class="badge badge-blue"><?= ucfirst($a['type']) ?></span></td>
            <td>#<?= $a['reference_id'] ?></td>
            <td><span class="badge badge-<?= $c ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span></td>
            <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y',strtotime($a['created_at'])) ?></td>
            <td><a href="applications.php?update=<?= $a['id'] ?>" class="btn-icon edit" title="Update"><i class="fas fa-edit"></i></a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
