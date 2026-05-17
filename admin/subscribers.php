<?php
$adminTitle = 'Newsletter Subscribers';
require_once 'header.php';
$db = getDB();
if(isset($_GET['remove'])){ $db->prepare("UPDATE subscribers SET is_active=0 WHERE id=?")->execute([(int)$_GET['remove']]); redirect(SITE_URL.'/admin/subscribers.php'); }
$subs=$db->query("SELECT * FROM subscribers WHERE is_active=1 ORDER BY subscribed_at DESC")->fetchAll();
$total=count($subs);
?>
<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h3 style="font-size:1rem;font-weight:700;">Subscribers (<?= $total ?>)</h3>
        <a href="?export=1" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export CSV</a>
    </div>
    <table>
        <thead><tr><th>Email</th><th>Name</th><th>Country</th><th>Subscribed</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach($subs as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['name']??'—') ?></td>
            <td><?= htmlspecialchars($s['country']??'—') ?></td>
            <td style="font-size:.8rem;color:#6b7280"><?= date('d M Y',strtotime($s['subscribed_at'])) ?></td>
            <td><a href="?remove=<?= $s['id'] ?>" class="btn-icon delete" onclick="return confirm('Remove subscriber?')" title="Remove"><i class="fas fa-times"></i></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if(!$subs): ?><tr><td colspan="5" style="text-align:center;color:#6b7280;padding:2rem;">No subscribers yet.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once 'footer.php'; ?>
