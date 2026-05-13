<?php
$pageTitle = 'Forgot Password';
require_once 'includes/config.php';
startSecureSession();
if(isLoggedIn()) redirect(SITE_URL.'/dashboard.php');
$msg = $error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!validateCSRF($_POST['csrf']??'')){ $error='Security error.'; }
    else {
        $email=strtolower(trim($_POST['email']??''));
        $db=getDB();
        $stmt=$db->prepare("SELECT id FROM users WHERE email=? AND status='active'");
        $stmt->execute([$email]);$user=$stmt->fetch();
        // Always show success for security
        $msg='If that email is registered, you will receive a password reset link. Please check your email or contact us via WhatsApp for immediate help.';
        if($user){
            $token=generateToken(32);
            $expires=date('Y-m-d H:i:s',strtotime('+1 hour'));
            $db->prepare("UPDATE users SET reset_token=?,reset_expires=? WHERE id=?")->execute([$token,$expires,$user['id']]);
            // In production: send email with reset link
            // For now: admin handles via WhatsApp
        }
    }
}
require_once 'includes/header.php';
?>
<div class="page-header" style="padding:3rem 0 2rem;"><div class="container text-center"><h1>Forgot Password</h1></div></div>
<section style="padding:3rem 0 5rem;">
<div class="container">
    <div class="form-section">
        <div style="text-align:center;margin-bottom:2rem;">
            <div style="font-size:3rem;">🔑</div>
            <h2 style="font-family:var(--font-display);margin-bottom:.25rem;">Reset Password</h2>
            <p style="color:var(--text-muted);font-size:.9rem;">Enter your email and we'll send a reset link.</p>
        </div>
        <?php if($msg): ?><div style="background:#dcfce7;color:#166534;padding:.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;font-size:.875rem;"><?= $msg ?></div><?php endif; ?>
        <?php if($error): ?><div style="background:#fee2e2;color:#991b1b;padding:.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;font-size:.875rem;"><?= $error ?></div><?php endif; ?>
        <?php if(!$msg): ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
            <div class="form-group"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" required placeholder="your@email.com"></div>
            <button type="submit" class="btn btn-green btn-block btn-lg"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
        </form>
        <?php endif; ?>
        <div style="text-align:center;margin-top:1.5rem;">
            <p style="font-size:.875rem;color:var(--text-muted);">Need immediate help? <a href="<?= WHATSAPP_LINK ?>?text=I+forgot+my+password" target="_blank" style="color:#25d366;font-weight:600;"><i class="fab fa-whatsapp"></i> WhatsApp us</a></p>
            <p style="font-size:.875rem;color:var(--text-muted);margin-top:.5rem;"><a href="login.php" style="color:var(--primary);">← Back to Login</a></p>
        </div>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
