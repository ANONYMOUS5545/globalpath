<?php
$pageTitle = 'Scholarship Detail';
require_once 'includes/config.php';
require_once 'includes/opportunity_sync.php';
startSecureSession();
$user = getCurrentUser();
$db = getDB();
try { ensureOpportunitySyncSchema($db); } catch (Throwable $e) {}
$id = (int)($_GET['id']??0);
$s = $db->prepare("SELECT * FROM scholarships WHERE id=? AND is_active=1");
$s->execute([$id]);
$scholarship = $s->fetch();
if(!$scholarship){ header('Location: scholarships.php'); exit; }
$pageTitle = htmlspecialchars($scholarship['title']);
require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="index.php">Home</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><a href="scholarships.php">Scholarships</a><i class="fas fa-chevron-right" style="font-size:.65rem"></i><span><?= htmlspecialchars(substr($scholarship['title'],0,40)) ?>...</span></div>
        <h1><?= htmlspecialchars($scholarship['title']) ?></h1>
        <p>by <?= htmlspecialchars($scholarship['provider']) ?> — <?= htmlspecialchars($scholarship['country']) ?></p>
    </div>
</div>
<section>
<div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">
        <div>
            <div style="background:white;border-radius:var(--radius);padding:2rem;border:1px solid var(--border);margin-bottom:1.5rem;">
                <div class="card-meta" style="margin-bottom:1.25rem;">
                    <span class="badge badge-green"><?= ucfirst($scholarship['type']) ?></span>
                    <span class="badge badge-blue"><?= ucfirst($scholarship['level']) ?></span>
                    <?php if(($scholarship['listing_origin'] ?? 'manual') === 'imported'): ?><span class="badge badge-green"><i class="fas fa-signal"></i> Live Feed</span><?php endif; ?>
                    <?php if($scholarship['is_featured']): ?><span class="badge badge-gold"><i class="fas fa-star"></i> Featured</span><?php endif; ?>
                </div>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Description</h3>
                <p style="line-height:1.8;color:var(--text);margin-bottom:1.5rem;"><?= nl2br(htmlspecialchars($scholarship['description'])) ?></p>
                <?php if($scholarship['eligibility']): ?>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Eligibility</h3>
                <p style="line-height:1.8;color:var(--text);margin-bottom:1.5rem;"><?= nl2br(htmlspecialchars($scholarship['eligibility'])) ?></p>
                <?php endif; ?>
                <?php if($scholarship['benefits']): ?>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Benefits</h3>
                <p style="line-height:1.8;color:var(--text);margin-bottom:1.5rem;"><?= nl2br(htmlspecialchars($scholarship['benefits'])) ?></p>
                <?php endif; ?>
                <?php if($scholarship['field_of_study']): ?>
                <h3 style="font-family:var(--font-ui);margin-bottom:.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;font-size:.8rem;">Fields of Study</h3>
                <p style="line-height:1.8;color:var(--text);"><?= htmlspecialchars($scholarship['field_of_study']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div style="position:sticky;top:90px;">
            <div style="background:white;border-radius:var(--radius);padding:1.75rem;border:2px solid var(--primary);margin-bottom:1rem;">
                <div style="font-size:3rem;text-align:center;margin-bottom:1rem;">🎓</div>
                <?php if($scholarship['deadline']): ?>
                <div style="background:#fef3c7;padding:.75rem;border-radius:var(--radius-sm);margin-bottom:1rem;text-align:center;">
                    <i class="fas fa-clock" style="color:#b45309"></i>
                    <strong style="color:#b45309;">Deadline:</strong> <?= date('d M Y',strtotime($scholarship['deadline'])) ?>
                    <?php $dl=floor((strtotime($scholarship['deadline'])-time())/86400);
                    if($dl>0 && $dl<=30) echo "<br><span style='color:var(--accent);font-size:.8rem;'>⚡ $dl days remaining!</span>"; ?>
                </div>
                <?php endif; ?>
                <div style="font-size:.875rem;margin-bottom:1rem;">
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Provider</span><strong><?= htmlspecialchars($scholarship['provider']) ?></strong></div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Country</span><strong><?= htmlspecialchars($scholarship['country']) ?></strong></div>
                    <?php if(!empty($scholarship['source_org'])): ?><div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Source</span><strong><?= htmlspecialchars($scholarship['source_org']) ?></strong></div><?php endif; ?>
                    <?php if(!empty($scholarship['published_at'])): ?><div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Updated</span><strong><?= htmlspecialchars(timeAgo($scholarship['published_at'])) ?></strong></div><?php endif; ?>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-muted);">Level</span><strong><?= ucfirst($scholarship['level']) ?></strong></div>
                    <div style="display:flex;justify-content:space-between;padding:.4rem 0;"><span style="color:var(--text-muted);">Type</span><strong><?= ucfirst($scholarship['type']) ?></strong></div>
                </div>
                <?php if($scholarship['link']): ?>
                <a href="<?= htmlspecialchars($scholarship['link']) ?>" target="_blank" class="btn btn-green btn-block" style="margin-bottom:.75rem;justify-content:center;"><i class="fas fa-external-link-alt"></i> <?= ($scholarship['listing_origin'] ?? 'manual') === 'imported' ? 'Open Source Listing' : 'Apply on Official Site' ?></a>
                <?php endif; ?>
                <a href="scholarship-support.php" class="btn btn-outline btn-block" style="justify-content:center;"><i class="fas fa-hands-helping"></i> Get Application Support</a>
            </div>
            <div style="background:white;border-radius:var(--radius);padding:1.25rem;border:1px solid var(--border);text-align:center;">
                <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:.75rem;">Need help applying? WhatsApp us:</p>
                <a href="<?= WHATSAPP_LINK ?>?text=I+need+help+applying+for+<?= urlencode($scholarship['title']) ?>" target="_blank" class="btn" style="background:#25d366;color:white;justify-content:center;width:100%;"><i class="fab fa-whatsapp"></i> WhatsApp Support</a>
            </div>
        </div>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
