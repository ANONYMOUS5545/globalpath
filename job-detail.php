<?php
$pageTitle = 'Job Detail';
require_once 'includes/config.php';
require_once 'includes/opportunity_sync.php';
require_once 'includes/content_bootstrap.php';
startSecureSession();

$user = getCurrentUser();
$membershipType = $user['membership_type'] ?? 'free';
$db = getDB();
bootSiteContent($db);
try { ensureOpportunitySyncSchema($db); } catch (Throwable $e) {}

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM jobs WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
    redirect(SITE_URL . '/jobs.php');
}

if (!userCanAccessJob($user, $job)) {
    redirect(SITE_URL . '/jobs.php?locked=1');
}

$pageTitle = htmlspecialchars($job['title']);
$applied = false;
if ($user) {
    $chk = $db->prepare("SELECT id FROM applications WHERE user_id = ? AND type = 'job' AND reference_id = ?");
    $chk->execute([$user['id'], $id]);
    $applied = (bool)$chk->fetch();
}

$csrf = generateCSRF();
$deadlineMeta = jobDeadlineMeta($job);
$jobTier = $job['access_tier'] ?? (($job['is_premium_only'] ?? 0) ? 'premium' : 'free');
$deadlinePassed = hasJobDeadlinePassed($job);

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><a href="jobs.php">Jobs</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><span><?= htmlspecialchars(substr($job['title'],0,40)) ?>...</span></div>
        <h1><?= htmlspecialchars($job['title']) ?></h1>
        <p><?= htmlspecialchars($job['organization']) ?> - <?= htmlspecialchars($job['location']) ?></p>
    </div>
</div>

<section>
<div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">
        <div>
            <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);margin-bottom:1.5rem;">
                <div class="card-meta" style="margin-bottom:1.25rem;">
                    <span class="badge <?= jobAccessTierBadgeClass($jobTier) ?>"><?= htmlspecialchars(jobAccessTierLabel($jobTier)) ?></span>
                    <?php if(($job['listing_origin'] ?? 'manual') === 'imported'): ?><span class="badge badge-green"><i class="fas fa-signal"></i> Live Feed</span><?php endif; ?>
                    <span class="badge badge-blue"><?= str_replace('_',' ',ucfirst($job['job_type'])) ?></span>
                    <span class="badge badge-gray"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['country']) ?></span>
                    <?php if($job['sector']): ?><span class="badge badge-green"><?= htmlspecialchars($job['sector']) ?></span><?php endif; ?>
                </div>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Job Description</h3>
                <p style="line-height:1.8;color:var(--text);margin-bottom:1.5rem;"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                <?php if($job['requirements']): ?>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Requirements</h3>
                <p style="line-height:1.8;color:var(--text);"><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div style="position:sticky;top:90px;">
            <div style="background:white;border-radius:var(--radius);padding:1.75rem;border:2px solid var(--primary);margin-bottom:1rem;">
                <div style="font-size:2.5rem;text-align:center;margin-bottom:1rem;">💼</div>
                <div style="background:<?= $deadlineMeta['class'] === 'passed' ? '#fee2e2' : '#fef3c7' ?>;padding:.75rem;border-radius:var(--radius-sm);margin-bottom:1rem;text-align:center;">
                    <i class="fas fa-clock" style="color:<?= $deadlineMeta['class'] === 'passed' ? '#b91c1c' : '#b45309' ?>"></i>
                    <strong style="color:<?= $deadlineMeta['class'] === 'passed' ? '#b91c1c' : '#b45309' ?>;"><?= htmlspecialchars($deadlineMeta['label']) ?></strong>
                </div>
                <?php if($job['salary_range']): ?>
                <div style="background:#dcfce7;padding:.75rem;border-radius:var(--radius-sm);margin-bottom:1rem;text-align:center;font-weight:600;color:var(--primary);">
                    <i class="fas fa-dollar-sign"></i> <?= htmlspecialchars($job['salary_range']) ?>
                </div>
                <?php endif; ?>
                <div style="font-size:.875rem;margin-bottom:1.25rem;">
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Organisation</span><strong><?= htmlspecialchars($job['organization']) ?></strong></div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Location</span><strong><?= htmlspecialchars($job['location']) ?></strong></div>
                    <?php if(!empty($job['source_org'])): ?><div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Source</span><strong><?= htmlspecialchars($job['source_org']) ?></strong></div><?php endif; ?>
                    <?php if(!empty($job['published_at'])): ?><div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Updated</span><strong><?= htmlspecialchars(timeAgo($job['published_at'])) ?></strong></div><?php endif; ?>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Access</span><strong><?= htmlspecialchars(jobAccessTierLabel($jobTier)) ?></strong></div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;"><span style="color:var(--text-muted);">Type</span><strong><?= str_replace('_',' ',ucfirst($job['job_type'])) ?></strong></div>
                </div>

                <?php if($deadlinePassed): ?>
                    <div class="btn btn-outline btn-block" style="justify-content:center;margin-bottom:.75rem;cursor:default;"><i class="fas fa-clock"></i> Deadline Passed</div>
                <?php elseif(!$user): ?>
                    <a href="login.php?redirect=<?= urlencode(SITE_URL.'/job-detail.php?id='.$id) ?>" class="btn btn-green btn-block" style="justify-content:center;margin-bottom:.75rem;"><i class="fas fa-sign-in-alt"></i> Login to Apply</a>
                <?php elseif($user['membership_type']==='free'): ?>
                    <a href="membership.php#premium" class="btn btn-primary btn-block" style="justify-content:center;margin-bottom:.75rem;"><i class="fas fa-star"></i> Upgrade to Apply</a>
                <?php elseif($applied): ?>
                    <div class="btn btn-outline btn-block" style="justify-content:center;margin-bottom:.75rem;cursor:default;"><i class="fas fa-check"></i> Already Applied</div>
                <?php else: ?>
                    <button onclick="applyNow('job',<?= $id ?>,'<?= $csrf ?>')" data-apply="<?= $id ?>" class="btn btn-green btn-block" style="justify-content:center;margin-bottom:.75rem;"><i class="fas fa-paper-plane"></i> Apply Now</button>
                <?php endif; ?>

                <?php if($job['link']): ?>
                <a href="<?= htmlspecialchars($job['link']) ?>" target="_blank" class="btn btn-outline btn-block" style="justify-content:center;"><i class="fas fa-external-link-alt"></i> Official Job Posting</a>
                <?php endif; ?>
            </div>

            <div style="background:white;border-radius:var(--radius);padding:1.25rem;border:1px solid var(--border);margin-bottom:1rem;">
                <h3 style="font-family:var(--font-ui);font-size:1rem;margin-bottom:.5rem;">Premium CV Support</h3>
                <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.75rem;">Premium and Premium Plus members can get sector-specific CV drafting help for healthcare, sea jobs, remote work, NGO roles and tech applications.</p>
                <a href="membership.php#cv-support" class="btn btn-primary btn-block" style="justify-content:center;">See CV Support</a>
            </div>

            <div style="background:white;border-radius:var(--radius);padding:1.25rem;border:1px solid var(--border);text-align:center;">
                <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.75rem;">Questions? Chat with us:</p>
                <a href="<?= WHATSAPP_LINK ?>?text=I+have+a+question+about+<?= urlencode($job['title']) ?>" target="_blank" class="btn" style="background:#25d366;color:white;justify-content:center;width:100%;"><i class="fab fa-whatsapp"></i> WhatsApp Support</a>
            </div>
        </div>
    </div>
</div>
</section>
<script>
document.body.dataset.loggedIn = '<?= $user ? '1' : '' ?>';
</script>
<?php require_once 'includes/footer.php'; ?>
