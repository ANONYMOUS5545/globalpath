<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false,'message'=>'Security error.']); exit; }
$email = strtolower(trim($input['email']??''));
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){ echo json_encode(['success'=>false,'message'=>'Invalid email address.']); exit; }
$db = getDB();
try {
    $db->prepare("INSERT INTO subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active=1")->execute([$email]);
    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>'Could not subscribe. Please try again.']);
}
