<?php
require_once '../includes/config.php';
startSecureSession();
if(isAdmin()) { redirect(SITE_URL.'/admin/dashboard.php'); }

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!validateCSRF($_POST['csrf']??'')){ $error='Security error.'; }
    else {
        $email=strtolower(trim($_POST['email']??''));
        $pw=$_POST['password']??'';
        $db=getDB();
        $stmt=$db->prepare("SELECT * FROM admins WHERE email=?");
        $stmt->execute([$email]);
        $admin=$stmt->fetch();
        if($admin && verifyPassword($pw,$admin['password_hash'])){
            $_SESSION['admin_id']=$admin['id'];
            $_SESSION['admin_name']=$admin['name'];
            $_SESSION['admin_role']=$admin['role'];
            $db->prepare("UPDATE admins SET last_login=NOW() WHERE id=?")->execute([$admin['id']]);
            redirect(SITE_URL.'/admin/dashboard.php');
        } else { $error='Invalid credentials.'; sleep(1); }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login — <?= SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#0f4526,#1a6b3c);min-height:100vh;display:flex;align-items:center;justify-content:center;}
.box{background:white;border-radius:16px;padding:2.5rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.logo{text-align:center;margin-bottom:2rem;}
.logo .icon{font-size:3rem;margin-bottom:.5rem;}
.logo h1{font-size:1.5rem;color:#1a6b3c;}
.logo p{color:#6b7280;font-size:.875rem;}
.form-group{margin-bottom:1.25rem;}
label{display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.4rem;}
input{width:100%;padding:.75rem 1rem;border:2px solid #e5e7eb;border-radius:8px;font-size:.95rem;outline:none;transition:.2s;}
input:focus{border-color:#1a6b3c;}
.btn{width:100%;padding:.85rem;background:#1a6b3c;color:white;border:none;border-radius:50px;font-size:1rem;font-weight:600;cursor:pointer;transition:.2s;}
.btn:hover{background:#0f4526;}
.error{background:#fee2e2;color:#991b1b;padding:.75rem 1rem;border-radius:8px;font-size:.875rem;margin-bottom:1rem;}
</style>
</head>
<body>
<div class="box">
    <div class="logo">
        <div class="icon">🌍</div>
        <h1>Admin Panel</h1>
        <p>Global Path Africa</p>
    </div>
    <?php if($error): ?><div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= generateCSRF() ?>">
        <div class="form-group"><label>Admin Email</label><input type="email" name="email" required placeholder="admin@globalpathAfrica.org" autofocus></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required placeholder="••••••••"></div>
        <button type="submit" class="btn"><i class="fas fa-lock"></i> Sign In</button>
    </form>
    <p style="text-align:center;margin-top:1.5rem;font-size:.8rem;color:#9ca3af;"><a href="../index.php" style="color:#1a6b3c;">← Back to Website</a></p>
</div>
</body>
</html>
