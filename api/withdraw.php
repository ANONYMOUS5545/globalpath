<?php
require_once '../includes/config.php';
startSecureSession();
if(!isLoggedIn()){ redirect(SITE_URL.'/login.php'); }
if(!validateCSRF($_POST['csrf']??'')){ redirect(SITE_URL.'/applications.php'); }
$appId=(int)($_POST['app_id']??0);
$db=getDB();
$stmt=$db->prepare("UPDATE applications SET status='withdrawn' WHERE id=? AND user_id=? AND status='submitted'");
$stmt->execute([$appId,$_SESSION['user_id']]);
redirect(SITE_URL.'/applications.php');
