<?php
$pageTitle = 'Payment History';
require_once 'includes/config.php';
startSecureSession();
if(!isLoggedIn()) redirect(SITE_URL.'/login.php');
$user = getCurrentUser();
$db = getDB();
$stmt = $db->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY payment_date DESC");
$stmt->execute([$user['id']]);
$payments = $stmt->fetchAll();
require_once 'includes/header.php';
?>
<div class="page-header" style="padding:3rem 0 2rem;"><div class="container"><h1>Payment History</h1></div></div>
<section style="padding:3rem 0 5rem;">
<div class="container">
    <div class="dashboard-layout">
        <aside class="sidebar">
            <nav><ul class="sidebar-nav">
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="payments.php" class="active"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="membership.php"><i class="fas fa-star"></i> Membership</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                <hr class="sidebar-divider">
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul></nav>
        </aside>
        <div class="dashboard-main">
            <div style="background:white;border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);">
                <h3 style="font-family:var(--font-ui);font-size:1rem;font-weight:700;margin-bottom:1.25rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">All Payments</h3>
                <?php if($payments): ?>
                <table class="data-table">
                    <thead><tr><th>Plan</th><th>Amount</th><th>Gateway</th><th>Transaction ID</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach($payments as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars(formatPlanName($p['plan'])) ?></td>
                        <td><?= formatCurrency($p['amount'],$p['currency']) ?></td>
                        <td style="text-transform:capitalize;"><?= htmlspecialchars($p['gateway']) ?></td>
                        <td style="font-size:.78rem;color:var(--text-muted);font-family:monospace;"><?= htmlspecialchars(substr($p['transaction_id'],0,20)) ?>...</td>
                        <td><span class="badge badge-<?= $p['status']==='completed'?'green':($p['status']==='pending'?'gold':'red') ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td style="font-size:.82rem;color:var(--text-muted);"><?= date('d M Y',strtotime($p['payment_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:1rem;">💳</div>
                    <p>No payments yet. <a href="membership.php" style="color:var(--primary)">View membership plans</a></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
