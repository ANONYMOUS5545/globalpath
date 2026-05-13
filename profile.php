<?php
$pageTitle = 'Edit Profile';
require_once 'includes/config.php';
startSecureSession();
if(!isLoggedIn()) redirect(SITE_URL.'/login.php');
$user = getCurrentUser();
$db = getDB();
$countries = $db->query("SELECT name FROM african_countries ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$success = $error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!validateCSRF($_POST['csrf']??'')){ $error='Security error.'; }
    else {
        $first = sanitize($_POST['first_name']??'');
        $last  = sanitize($_POST['last_name']??'');
        $phone = sanitize($_POST['phone']??'');
        $country=sanitize($_POST['country']??'');
        $nat   = sanitize($_POST['nationality']??'');
        $newpw = $_POST['new_password']??'';
        $conpw = $_POST['confirm_password']??'';
        if(!$first||!$last){ $error='Name fields are required.'; }
        elseif($newpw && strlen($newpw)<8){ $error='Password must be 8+ characters.'; }
        elseif($newpw && $newpw!==$conpw){ $error='Passwords do not match.'; }
        else {
            if($newpw){
                $db->prepare("UPDATE users SET first_name=?,last_name=?,phone=?,country=?,nationality=?,password_hash=? WHERE id=?")
                   ->execute([$first,$last,$phone,$country,$nat,hashPassword($newpw),$user['id']]);
            } else {
                $db->prepare("UPDATE users SET first_name=?,last_name=?,phone=?,country=?,nationality=? WHERE id=?")
                   ->execute([$first,$last,$phone,$country,$nat,$user['id']]);
            }
            $_SESSION['user_country']=$country;
            $success='Profile updated successfully!';
            $user = getCurrentUser();
        }
    }
}
require_once 'includes/header.php';
?>
<div class="page-header" style="padding:3rem 0 2rem;"><div class="container"><h1>Edit Profile</h1></div></div>
<section style="padding:3rem 0 5rem;">
<div class="container">
    <div class="form-section" style="max-width:640px;">
        <div style="text-align:center;margin-bottom:2rem;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:white;font-size:2rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <?= strtoupper(substr($user['first_name'],0,1).substr($user['last_name'],0,1)) ?>
            </div>
            <h2 style="font-family:var(--font-display);">Your Profile</h2>
        </div>
        <?php if($success): ?><div style="background:#dcfce7;color:#166534;padding:.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div style="background:#fee2e2;color:#991b1b;padding:.9rem 1.25rem;border-radius:var(--radius-sm);margin-bottom:1.25rem;"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
            <div class="form-row">
                <div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($user['first_name']) ?>"></div>
                <div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($user['last_name']) ?>"></div>
            </div>
            <div class="form-group"><label class="form-label">Email (cannot change)</label><input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:.6"></div>
            <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-control" placeholder="+254 700 000 000" value="<?= htmlspecialchars($user['phone']??'') ?>"></div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Country of Residence</label>
                    <select name="country" class="form-control">
                        <?php foreach($countries as $c): ?><option value="<?= htmlspecialchars($c) ?>" <?= $user['country']===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Nationality</label>
                    <select name="nationality" class="form-control">
                        <?php foreach($countries as $c): ?><option value="<?= htmlspecialchars($c) ?>" <?= $user['nationality']===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr style="margin:1.5rem 0;border-color:var(--border)">
            <h4 style="font-family:var(--font-ui);margin-bottom:1rem;color:var(--text-muted);">Change Password (optional)</h4>
            <div class="form-row">
                <div class="form-group"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" placeholder="Min. 8 characters"></div>
                <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" placeholder="Repeat password"></div>
            </div>
            <button type="submit" class="btn btn-green btn-block btn-lg"><i class="fas fa-save"></i> Save Changes</button>
        </form>
    </div>
</div>
</section>
<?php require_once 'includes/footer.php'; ?>
