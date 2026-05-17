<?php
$pageTitle = 'My Dashboard';
require_once 'includes/config.php';
startSecureSession();

if (!isLoggedIn()) redirect(SITE_URL . '/login.php?redirect=' . urlencode(SITE_URL . '/dashboard.php'));

$user = getCurrentUser();
$db = getDB();

// Stats
$appCount = $db->prepare("SELECT COUNT(*) FROM applications WHERE user_id=?")->execute([$user['id']]) ? 
    $db->prepare("SELECT COUNT(*) FROM applications WHERE user_id=?")->execute([$user['id']]) : 0;
$stmt = $db->prepare("SELECT COUNT(*) FROM applications WHERE user_id=?");
$stmt->execute([$user['id']]);
$appCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM applications WHERE user_id=? AND status='accepted'");
$stmt->execute([$user['id']]);
$successCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM payments WHERE user_id=? AND status='completed'");
$stmt->execute([$user['id']]);
$payCount = $stmt->fetchColumn();

// Recent Applications
$stmt = $db->prepare("SELECT * FROM applications WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user['id']]);
$recentApps = $stmt->fetchAll();

// Recent Payments
$stmt = $db->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY payment_date DESC LIMIT 5");
$stmt->execute([$user['id']]);
$recentPayments = $stmt->fetchAll();

$welcome = isset($_GET['welcome']);
$membershipLabel = ['free'=>'Free','premium'=>'Premium','premium_plus'=>'Premium Plus'][$user['membership_type']] ?? 'Free';
$membershipColor = ['free'=>'#6b7280','premium'=>'var(--primary)','premium_plus'=>'var(--gold)'][$user['membership_type']] ?? '#6b7280';

require_once 'includes/header.php';
?>

<?php if ($welcome): ?>
<div style="background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;padding:1.25rem 0;text-align:center;" class="alert-auto-dismiss">
    <strong>🎉 Welcome to Global Path Africa, <?= htmlspecialchars($user['first_name']) ?>!</strong> Your account is ready. Start exploring opportunities below.
</div>
<?php endif; ?>

<div style="padding:2rem 0 4rem;">
    <div class="container">
        
        <!-- Dashboard Header -->
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
            <div>
                <h1 style="font-family:var(--font-display);font-size:1.75rem;margin-bottom:0.25rem;">
                    Welcome back, <?= htmlspecialchars($user['first_name']) ?>! 👋
                </h1>
                <p style="color:var(--text-muted);">
                    <span class="badge" style="background:<?= $membershipColor ?>;color:white;padding:0.3rem 0.9rem;">
                        <i class="fas fa-star"></i> <?= $membershipLabel ?> Member
                    </span>
                    &nbsp;<?= htmlspecialchars($user['country']) ?>
                    <?php if ($user['membership_expires']): ?>
                    &nbsp;· Expires: <?= date('d M Y', strtotime($user['membership_expires'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                <?php if ($user['membership_type'] === 'free'): ?>
                <a href="membership.php" class="btn btn-primary"><i class="fas fa-star"></i> Upgrade to Premium</a>
                <?php endif; ?>
                <a href="scholarships.php" class="btn btn-outline"><i class="fas fa-graduation-cap"></i> Browse Scholarships</a>
            </div>
        </div>
        
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div style="text-align:center;padding:1.25rem 0;border-bottom:1px solid var(--border);margin-bottom:1rem;">
                    <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;font-size:1.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;">
                        <?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?>
                    </div>
                    <div style="font-weight:600;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    <div style="font-size:0.78rem;color:var(--text-muted);"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <nav>
                    <ul class="sidebar-nav">
                        <li><a href="dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                        <li><a href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a></li>
                        <li><a href="scholarships.php"><i class="fas fa-graduation-cap"></i> Scholarships</a></li>
                        <li><a href="jobs.php"><i class="fas fa-briefcase"></i> Jobs Abroad</a></li>
                        <li><a href="visas.php"><i class="fas fa-passport"></i> Visa Resources</a></li>
                        <li><a href="language-classes.php"><i class="fas fa-language"></i> Language Classes</a></li>
                        <hr class="sidebar-divider">
                        <li><a href="payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                        <li><a href="membership.php"><i class="fas fa-star"></i> Membership</a></li>
                        <li><a href="profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                        <hr class="sidebar-divider">
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
                
                <?php if ($user['membership_type'] === 'free'): ?>
                <div class="membership-badge-sidebar">
                    <strong>⭐ Upgrade to Premium</strong>
                    <p style="font-size:0.78rem;margin:0.4rem 0;">Apply to jobs, get priority support</p>
                    <a href="membership.php" style="color:var(--gold-light);font-size:0.8rem;font-weight:600;">View Plans →</a>
                </div>
                <?php endif; ?>
                
                <div style="margin-top:1rem;padding:1rem;background:#f0faf5;border-radius:var(--radius-sm);text-align:center;">
                    <div style="font-size:1.25rem;margin-bottom:0.4rem;">💬</div>
                    <strong style="font-size:0.85rem;">Need Help?</strong>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin:0.3rem 0;">WhatsApp us directly</p>
                    <a href="<?= WHATSAPP_LINK ?>" target="_blank" style="display:inline-flex;align-items:center;gap:0.4rem;color:#25d366;font-size:0.82rem;font-weight:600;text-decoration:none;">
                        <i class="fab fa-whatsapp"></i> +254 792 579 974
                    </a>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="dashboard-main">
                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-file-alt"></i></div>
                        <div>
                            <div class="stat-value"><?= $appCount ?></div>
                            <div class="stat-label">Applications</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon gold"><i class="fas fa-trophy"></i></div>
                        <div>
                            <div class="stat-value"><?= $successCount ?></div>
                            <div class="stat-label">Accepted</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-credit-card"></i></div>
                        <div>
                            <div class="stat-value"><?= $payCount ?></div>
                            <div class="stat-label">Payments</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fas fa-star"></i></div>
                        <div>
                            <div class="stat-value" style="font-size:1rem;margin-top:0.2rem;"><?= $membershipLabel ?></div>
                            <div class="stat-label">Membership</div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div style="background:white;border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);">
                    <h3 style="font-family:var(--font-ui);font-size:1rem;font-weight:700;margin-bottom:1.25rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">Quick Actions</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
                        <a href="scholarships.php" class="btn btn-outline"><i class="fas fa-graduation-cap"></i> Find Scholarship</a>
                        <a href="jobs.php" class="btn btn-outline"><i class="fas fa-briefcase"></i> Browse Jobs</a>
                        <a href="membership.php#visa-support" class="btn btn-outline"><i class="fas fa-passport"></i> Visa Support</a>
                        <a href="language-classes.php" class="btn btn-outline"><i class="fas fa-language"></i> Language Class</a>
                        <a href="<?= WHATSAPP_LINK ?>" target="_blank" class="btn" style="background:#25d366;color:white;justify-content:center;"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    </div>
                </div>
                
                <!-- Recent Applications -->
                <div style="background:white;border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                        <h3 style="font-family:var(--font-ui);font-size:1rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">Recent Applications</h3>
                        <a href="applications.php" style="color:var(--primary);font-size:0.85rem;">View All →</a>
                    </div>
                    <?php if ($recentApps): ?>
                    <table class="data-table">
                        <thead>
                            <tr><th>Type</th><th>Ref. ID</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentApps as $app): 
                            $statusColors = ['submitted'=>'blue','under_review'=>'purple','accepted'=>'green','rejected'=>'red','withdrawn'=>'gray'];
                            $color = $statusColors[$app['status']] ?? 'gray';
                        ?>
                        <tr>
                            <td><span class="badge badge-blue"><?= ucfirst($app['type']) ?></span></td>
                            <td>#<?= $app['reference_id'] ?></td>
                            <td><span class="badge badge-<?= $color ?>"><?= ucfirst(str_replace('_',' ',$app['status'])) ?></span></td>
                            <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="text-align:center;padding:2rem;color:var(--text-muted);">
                        <div style="font-size:3rem;margin-bottom:1rem;">📋</div>
                        <p>No applications yet. Start browsing opportunities!</p>
                        <a href="scholarships.php" class="btn btn-green mt-2">Browse Scholarships</a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Membership Upgrade Banner (for free users) -->
                <?php if ($user['membership_type'] === 'free'): ?>
                <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:var(--radius);padding:2rem;color:white;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <h3 style="font-family:var(--font-display);font-size:1.4rem;margin-bottom:0.4rem;">Unlock Premium Features</h3>
                        <p style="opacity:0.8;font-size:0.9rem;">Apply to jobs, get application support, and access job postings first.</p>
                    </div>
                    <a href="membership.php" class="btn btn-primary">Upgrade Now — from $<?= PRICE_PREMIUM_MONTHLY ?>/mo</a>
                </div>
                <?php endif; ?>
                
                <!-- Recent Payments -->
                <?php if ($recentPayments): ?>
                <div style="background:white;border-radius:var(--radius);padding:1.5rem;border:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                        <h3 style="font-family:var(--font-ui);font-size:1rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;">Payment History</h3>
                        <a href="payments.php" style="color:var(--primary);font-size:0.85rem;">View All →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Plan</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentPayments as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars(str_replace('_',' ', ucwords($p['plan']))) ?></td>
                            <td><?= formatCurrency($p['amount'], $p['currency']) ?></td>
                            <td style="text-transform:capitalize;"><?= htmlspecialchars($p['gateway']) ?></td>
                            <td><span class="badge badge-<?= $p['status']==='completed'?'green':'gold' ?>"><?= ucfirst($p['status']) ?></span></td>
                            <td style="color:var(--text-muted);font-size:0.82rem;"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
