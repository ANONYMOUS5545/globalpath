<?php
$adminTitle = 'Support Messages';
require_once 'header.php';
$db = getDB();

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reply'])){
    $db->prepare("UPDATE support_messages SET reply=?,status='replied',replied_at=NOW() WHERE id=?")
       ->execute([sanitize($_POST['reply_text']??''),(int)$_POST['msg_id']]);
    redirect(SITE_URL.'/admin/messages.php?saved=1');
}
if(isset($_GET['close'])){ $db->prepare("UPDATE support_messages SET status='closed' WHERE id=?")->execute([(int)$_GET['close']]); redirect(SITE_URL.'/admin/messages.php'); }

$filter=sanitize($_GET['filter']??'open');
$where=$filter?"WHERE status=?":'';
$params=$filter?[$filter]:[];
$msgs=$db->prepare("SELECT m.*,u.first_name,u.last_name FROM support_messages m LEFT JOIN users u ON m.user_id=u.id $where ORDER BY m.created_at DESC LIMIT 50");
$msgs->execute($params);$messages=$msgs->fetchAll();
?>
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;">
    <?php foreach(['open'=>'Open','replied'=>'Replied','closed'=>'Closed',''=>'All'] as $v=>$l): ?>
    <a href="?filter=<?= $v ?>" class="btn btn-sm <?= $filter===$v?'btn-primary':'btn-outline' ?>"><?= $l ?></a>
    <?php endforeach; ?>
</div>
<div style="display:flex;flex-direction:column;gap:1rem;">
<?php foreach($messages as $m): ?>
<div class="admin-card" style="padding:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem;">
        <div>
            <strong><?= htmlspecialchars($m['name']??($m['first_name']??'Guest').' '.($m['last_name']??'')) ?></strong>
            <span style="color:#6b7280;font-size:.82rem;margin-left:.75rem;"><?= htmlspecialchars($m['email']??'') ?></span>
            <?php if($m['is_escalated']): ?><span class="badge badge-red" style="margin-left:.5rem;">⚠️ Escalated</span><?php endif; ?>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="badge badge-<?= $m['status']==='open'?'gold':($m['status']==='replied'?'green':'gray') ?>"><?= ucfirst($m['status']) ?></span>
            <span style="font-size:.78rem;color:#6b7280"><?= date('d M Y H:i',strtotime($m['created_at'])) ?></span>
            <a href="?close=<?= $m['id'] ?>" class="btn-icon delete" title="Close"><i class="fas fa-times"></i></a>
        </div>
    </div>
    <div style="background:#f8fafc;padding:.75rem 1rem;border-radius:8px;font-size:.875rem;margin-bottom:.75rem;border-left:3px solid #1a6b3c;"><?= htmlspecialchars($m['message']) ?></div>
    <?php if($m['reply']): ?>
    <div style="background:#dcfce7;padding:.75rem 1rem;border-radius:8px;font-size:.875rem;margin-bottom:.75rem;border-left:3px solid #16a34a;"><strong>Reply:</strong> <?= htmlspecialchars($m['reply']) ?></div>
    <?php endif; ?>
    <?php if($m['status']!=='closed'): ?>
    <form method="POST" style="display:flex;gap:.75rem;">
        <input type="hidden" name="msg_id" value="<?= $m['id'] ?>">
        <input type="text" name="reply_text" placeholder="Type a reply..." style="flex:1;padding:.5rem .85rem;border:2px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none;" required>
        <button type="submit" name="reply" value="1" class="btn btn-primary btn-sm">Send Reply</button>
        <a href="<?= WHATSAPP_LINK ?>?text=Replying+to+your+enquiry" target="_blank" class="btn btn-sm" style="background:#25d366;color:white;"><i class="fab fa-whatsapp"></i></a>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php if(!$messages): ?><div class="admin-card" style="text-align:center;color:#6b7280;padding:3rem;">No messages found.</div><?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
