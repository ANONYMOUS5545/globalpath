<?php
$pageTitle = 'My Applications';
require_once 'includes/config.php';
startSecureSession();
if(!isLoggedIn()) redirect(SITE_URL.'/login.php');
$user = getCurrentUser();
$db = getDB();
$stmt = $db->prepare("SELECT * FROM applications WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$apps = $stmt->fetchAll();
require_once 'includes/header.php';
?>
<div class="page-header" style="padding:3rem 0 2rem;"><div class="container"><h1>My Applications</h1></div></div>
<section style="padding:3rem 0 5rem;">
<div class="container">
    <div class="dashboard-layout">
        <aside class="sidebar">
            <nav><ul class="sidebar-nav">
                <li><a href="dashboard.php"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="applications.php" class="active"><i class="fas fa-file-alt"></i> My Applications</a></li>
                <li><a href="scholarships.php"><i class="fas fa-graduation-cap"></i> Scholarships</a></li>
                <li><a href="jobs.php"><i class="fas fa-briefcase"></i> Jobs Abroad</a></li>
                <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="membership.php"><i class="fas fa-star"></i> Membership</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                <hr class="sidebar-divider">
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul></nav>
        </aside>
        <div class="dashboard-main">
            <div style="background:white;border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);">
                <h3 style="font-family:var(--font-ui);font-size:1rem;font-weight:700;margin-bottom:1.25rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">All Applications</h3>
                <?php if($apps): ?>
                <table class="data-table">
                    <thead><tr><th>Type</th><th>Ref. ID</th><th>Status</th><th>Notes</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach($apps as $app):
                        $colors=['submitted'=>'blue','under_review'=>'purple','accepted'=>'green','rejected'=>'red','withdrawn'=>'gray'];
                        $c=$colors[$app['status']]??'gray';
                    ?>
                    <tr>
                        <td><span class="badge badge-blue"><?= ucfirst($app['type']) ?></span></td>
                        <td>#<?= $app['reference_id'] ?></td>
                        <td><span class="badge badge-<?= $c ?>"><?= ucfirst(str_replace('_',' ',$app['status'])) ?></span></td>
                        <td style="font-size:.82rem;color:var(--text-muted);"><?= htmlspecialchars(substr($app['notes']??'',0,40)) ?: '—' ?></td>
                        <td style="font-size:.82rem;color:var(--text-muted);"><?= date('d M Y',strtotime($app['created_at'])) ?></td>
                        <td>
                            <?php if($app['status']==='submitted'): ?>
                            <form method="POST" action="api/withdraw.php" style="display:inline;" onsubmit="return confirm('Withdraw this application?')">
                                <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
                                <button type="submit" class="btn-icon delete" title="Withdraw"><i class="fas fa-times"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center;padding:3rem;color:var(--text-muted);">
                    <div style="font-size:3rem;margin-bottom:1rem;">📋</div>
                    <p>No applications yet.</p>
                    <a href="scholarships.php" class="btn btn-green mt-2">Browse Scholarships</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
