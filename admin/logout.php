<?php
require_once '../includes/config.php';
startSecureSession();
unset($_SESSION['admin_id'],$_SESSION['admin_name'],$_SESSION['admin_role']);
redirect(SITE_URL.'/admin/login.php');
