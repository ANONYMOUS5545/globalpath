<?php
require_once '../includes/config.php';
startSecureSession();
if(!isAdmin()){ redirect(SITE_URL.'/admin/login.php'); }
$adminPage = basename($_SERVER['PHP_SELF'],'.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= isset($adminTitle)?htmlspecialchars($adminTitle).' — ':'' ?>Admin | <?= SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',sans-serif;background:#f1f5f9;color:#1a1a2e;}
.admin-layout{display:grid;grid-template-columns:240px 1fr;min-height:100vh;}
.admin-sidebar{background:#0d1117;padding:0;position:sticky;top:0;height:100vh;overflow-y:auto;}
.admin-logo{padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:.6rem;}
.admin-logo span{font-size:1.5rem;}
.admin-logo div{color:white;font-weight:700;font-size:1rem;line-height:1.2;}
.admin-logo small{color:#90f0b0;font-size:.65rem;letter-spacing:2px;text-transform:uppercase;}
.admin-nav{list-style:none;padding:.5rem 0;}
.admin-nav .section-label{padding:.75rem 1.5rem .25rem;font-size:.68rem;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:2px;}
.admin-nav li a{display:flex;align-items:center;gap:.6rem;padding:.65rem 1.5rem;text-decoration:none;color:rgba(255,255,255,.6);font-size:.875rem;transition:.2s;}
.admin-nav li a:hover,.admin-nav li a.active{color:white;background:rgba(255,255,255,.08);border-right:3px solid #2d9b5a;}
.admin-nav li a i{width:16px;font-size:.85rem;}
.admin-content{padding:2rem;overflow-x:hidden;}
.admin-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;}
.admin-title{font-size:1.5rem;font-weight:700;color:#1a1a2e;}
.admin-card{background:white;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.1);border:1px solid #e5e7eb;margin-bottom:1.5rem;}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;}
.stat-box{background:white;border-radius:12px;padding:1.25rem;border:1px solid #e5e7eb;display:flex;align-items:center;gap:1rem;}
.stat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;}
.stat-icon.green{background:#dcfce7;color:#166534}
.stat-icon.blue{background:#dbeafe;color:#1d4ed8}
.stat-icon.gold{background:#fef3c7;color:#b45309}
.stat-icon.purple{background:#ede9fe;color:#6d28d9}
.stat-val{font-size:1.75rem;font-weight:700;line-height:1}
.stat-lbl{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px}
table{width:100%;border-collapse:collapse;font-size:.875rem}
th{background:#f8fafc;padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;border-bottom:2px solid #e5e7eb}
td{padding:.85rem 1rem;border-bottom:1px solid #f0f0f0;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafafa}
.badge{display:inline-flex;align-items:center;gap:.25rem;padding:.2rem .6rem;border-radius:20px;font-size:.72rem;font-weight:600}
.badge-green{background:#dcfce7;color:#166534}.badge-blue{background:#dbeafe;color:#1e40af}
.badge-gold{background:#fef3c7;color:#92400e}.badge-red{background:#fee2e2;color:#991b1b}
.badge-gray{background:#f3f4f6;color:#374151}.badge-purple{background:#ede9fe;color:#5b21b6}
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:50px;font-size:.85rem;font-weight:600;text-decoration:none;cursor:pointer;border:2px solid transparent;transition:.2s}
.btn-primary{background:#1a6b3c;color:white;border-color:#1a6b3c}.btn-primary:hover{background:#0f4526}
.btn-danger{background:#ef4444;color:white;border-color:#ef4444}.btn-danger:hover{background:#dc2626}
.btn-outline{background:transparent;color:#1a6b3c;border-color:#1a6b3c}.btn-outline:hover{background:#1a6b3c;color:white}
.btn-sm{padding:.3rem .8rem;font-size:.78rem}
.btn-icon{width:30px;height:30px;border-radius:6px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.8rem;transition:.2s}
.btn-icon.edit{background:#dbeafe;color:#1d4ed8}.btn-icon.delete{background:#fee2e2;color:#991b1b}
.form-group{margin-bottom:1.1rem}
label.form-label{display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:.35rem}
input[type=text],input[type=email],input[type=number],input[type=date],input[type=url],textarea,select{width:100%;padding:.65rem .9rem;border:2px solid #e5e7eb;border-radius:8px;font-size:.9rem;outline:none;transition:.2s;font-family:inherit}
input:focus,textarea:focus,select:focus{border-color:#1a6b3c}
textarea{min-height:100px;resize:vertical}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.alert{padding:.9rem 1.1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem}
.alert-success{background:#dcfce7;color:#166534}.alert-error{background:#fee2e2;color:#991b1b}
.pagination{display:flex;gap:.4rem;justify-content:center;margin-top:1.5rem}
.page-btn{width:36px;height:36px;border-radius:6px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;transition:.2s}
.page-btn.active{background:#1a6b3c;border-color:#1a6b3c;color:white}
.page-btn:hover:not(.active){border-color:#1a6b3c;color:#1a6b3c}
.search-row{display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap}
.search-row input,.search-row select{flex:1;min-width:140px;padding:.55rem .85rem;border:2px solid #e5e7eb;border-radius:8px;font-size:.875rem;outline:none}
.search-row input:focus,.search-row select:focus{border-color:#1a6b3c}
@media(max-width:768px){.admin-layout{grid-template-columns:1fr}.admin-sidebar{display:none}.form-row{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="admin-layout">
<aside class="admin-sidebar">
    <div class="admin-logo"><span>🌍</span><div>Global Path<br><small>Admin Panel</small></div></div>
    <ul class="admin-nav">
        <li class="section-label">Main</li>
        <li><a href="dashboard.php" class="<?=$adminPage==='dashboard'?'active':''?>"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="users.php" class="<?=$adminPage==='users'?'active':''?>"><i class="fas fa-users"></i> Members</a></li>
        <li class="section-label">Content</li>
        <li><a href="scholarships.php" class="<?=$adminPage==='scholarships'?'active':''?>"><i class="fas fa-graduation-cap"></i> Scholarships</a></li>
        <li><a href="jobs.php" class="<?=$adminPage==='jobs'?'active':''?>"><i class="fas fa-briefcase"></i> Jobs</a></li>
        <li><a href="job-resources.php" class="<?=$adminPage==='job-resources'?'active':''?>"><i class="fas fa-link"></i> Job Resources</a></li>
        <li><a href="blog.php" class="<?=$adminPage==='blog'?'active':''?>"><i class="fas fa-newspaper"></i> Blog</a></li>
        <li><a href="applications.php" class="<?=$adminPage==='applications'?'active':''?>"><i class="fas fa-file-alt"></i> Applications</a></li>
        <li class="section-label">Finance</li>
        <li><a href="payments.php" class="<?=$adminPage==='payments'?'active':''?>"><i class="fas fa-credit-card"></i> Payments</a></li>
        <li class="section-label">Support</li>
        <li><a href="messages.php" class="<?=$adminPage==='messages'?'active':''?>"><i class="fas fa-comments"></i> Messages</a></li>
        <li><a href="subscribers.php" class="<?=$adminPage==='subscribers'?'active':''?>"><i class="fas fa-envelope"></i> Subscribers</a></li>
        <li class="section-label">System</li>
        <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
<div class="admin-content">
<div class="admin-topbar">
    <div class="admin-title"><?= $adminTitle??'Dashboard' ?></div>
    <div style="display:flex;align-items:center;gap:1rem;">
        <span style="font-size:.85rem;color:#6b7280;"><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['admin_name']??'Admin') ?> (<?= $_SESSION['admin_role']??'' ?>)</span>
        <a href="logout.php" class="btn btn-outline btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
