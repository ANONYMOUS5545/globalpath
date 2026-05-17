<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
startSecureSession();
if(!isLoggedIn()){ echo json_encode(['success'=>false,'message'=>'Please log in.']); exit; }
$input = json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false,'message'=>'Security error.']); exit; }

$phone = preg_replace('/\D/','',$input['phone']??'');
$plan  = sanitize($input['plan']??'');
if(strlen($phone)<9){ echo json_encode(['success'=>false,'message'=>'Invalid phone number.']); exit; }
// Normalise to 254XXXXXXXXX
if(substr($phone,0,1)==='0') $phone='254'.substr($phone,1);
if(substr($phone,0,3)!=='254') $phone='254'.$phone;

$details = getPlanDetails($plan);
$amount = $details['mpesa_amount_kes'] ?? 0;
if(!$amount){ echo json_encode(['success'=>false,'message'=>'Invalid plan.']); exit; }

// Get M-Pesa token
function getMpesaToken(){
    $key=MPESA_CONSUMER_KEY; $secret=MPESA_CONSUMER_SECRET;
    $ch=curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_USERPWD=>"$key:$secret",CURLOPT_TIMEOUT=>10]);
    $r=curl_exec($ch);curl_close($ch);
    $d=json_decode($r,true);
    return $d['access_token']??null;
}

$token=getMpesaToken();
if(!$token){ echo json_encode(['success'=>false,'message'=>'Payment service unavailable. Please try Bank Transfer or WhatsApp us.']); exit; }

$timestamp=date('YmdHis');
$password=base64_encode(MPESA_SHORTCODE.MPESA_PASSKEY.$timestamp);
$txRef='GPAf'.time().rand(100,999);

$user=getCurrentUser();
$ch=curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt_array($ch,[
    CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.$token],
    CURLOPT_POSTFIELDS=>json_encode([
        'BusinessShortCode'=>MPESA_SHORTCODE,'Password'=>$password,'Timestamp'=>$timestamp,
        'TransactionType'=>'CustomerPayBillOnline','Amount'=>$amount,'PartyA'=>$phone,
        'PartyB'=>MPESA_SHORTCODE,'PhoneNumber'=>$phone,'CallBackURL'=>MPESA_CALLBACK_URL,
        'AccountReference'=>$txRef,'TransactionDesc'=>'Global Path Africa - '.$plan
    ]),CURLOPT_TIMEOUT=>15
]);
$r=curl_exec($ch);$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);curl_close($ch);
$data=json_decode($r,true);

if($code===200 && ($data['ResponseCode']??'')==='0'){
    // Record pending payment
    $db=getDB();
    $db->prepare("INSERT INTO payments (user_id,transaction_id,gateway,amount,currency,plan,status,metadata) VALUES (?,?,?,?,?,?,?,?)")
       ->execute([$user['id'],$txRef,'mpesa',$amount,'KES',$plan,'pending',json_encode(['checkout_request_id'=>$data['CheckoutRequestID']??''])]);
    echo json_encode(['success'=>true,'message'=>'M-Pesa prompt sent!']);
} else {
    echo json_encode(['success'=>false,'message'=>'M-Pesa request failed. Please try again or contact support.']);
}
