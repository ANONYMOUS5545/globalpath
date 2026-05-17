<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
startSecureSession();
if(!isLoggedIn()){ echo json_encode(['success'=>false]); exit; }
$input=json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false,'message'=>'Security error.']); exit; }
$plan=sanitize($input['plan']??'');
$user=getCurrentUser();
$db=getDB();
$details = getPlanDetails($plan);
$amount = $details['amount_usd'] ?? 0;
if(!$amount){ echo json_encode(['success'=>false,'message'=>'Invalid plan.']); exit; }
$txRef='BANK-'.strtoupper(generateToken(8)).'-'.$user['id'];
$db->prepare("INSERT INTO payments (user_id,transaction_id,gateway,amount,currency,plan,status) VALUES (?,?,?,?,?,?,?)")
   ->execute([$user['id'],$txRef,'bank_transfer',$amount,'USD',$plan,'pending']);
echo json_encode(['success'=>true,'ref'=>$txRef]);
