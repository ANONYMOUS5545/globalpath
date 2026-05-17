<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
startSecureSession();
$input = json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false]); exit; }
$country = sanitize($input['country']??'');
if($country){
    $_SESSION['user_country']=$country;
    if(isLoggedIn()){
        getDB()->prepare("UPDATE users SET country=? WHERE id=?")->execute([$country,$_SESSION['user_id']]);
    }
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false]);
}
