<?php
// flutterwave_verify.php
require_once '../includes/config.php';
header('Content-Type: application/json');
startSecureSession();
if(!isLoggedIn()){ echo json_encode(['success'=>false]); exit; }
$input=json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false,'message'=>'Security error.']); exit; }
$txId=(int)($input['tx_id']??0);
$plan=sanitize($input['plan']??'');

// Verify with Flutterwave
$ch=curl_init("https://api.flutterwave.com/v3/transactions/{$txId}/verify");
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.FLUTTERWAVE_SECRET_KEY],CURLOPT_TIMEOUT=>15]);
$r=curl_exec($ch);curl_close($ch);
$data=json_decode($r,true);

if(($data['status']??'')!=='success'||($data['data']['status']??'')!=='successful'){
    echo json_encode(['success'=>false,'message'=>'Payment verification failed.']); exit;
}

$user=getCurrentUser();
$db=getDB();
$amount=$data['data']['amount']??0;
$currency=$data['data']['currency']??'USD';
$db->prepare("INSERT INTO payments (user_id,transaction_id,gateway,amount,currency,plan,status,metadata) VALUES (?,?,?,?,?,?,?,?)")
   ->execute([$user['id'],'FLW'.$txId,'flutterwave',$amount,$currency,$plan,'completed',json_encode(['flw_ref'=>$data['data']['flw_ref']??''])]);

activatePurchasedPlan($db, $user['id'], $plan);

echo json_encode(['success'=>true]);
