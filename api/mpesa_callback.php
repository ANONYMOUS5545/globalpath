<?php
// M-Pesa Callback — called by Safaricom after payment
require_once '../includes/config.php';
$payload = file_get_contents('php://input');
$data = json_decode($payload,true);
$result = $data['Body']['stkCallback']??null;
if(!$result) exit;

$code = $result['ResultCode']??-1;
$checkoutId = $result['CheckoutRequestID']??'';

if($code==0){
    $items = $result['CallbackMetadata']['Item']??[];
    $txId='';$amount=0;$phone='';
    foreach($items as $item){
        if($item['Name']==='MpesaReceiptNumber') $txId=$item['Value']??'';
        if($item['Name']==='Amount') $amount=$item['Value']??0;
        if($item['Name']==='PhoneNumber') $phone=$item['Value']??'';
    }
    $db=getDB();
    // Find pending payment by metadata
    $stmt=$db->prepare("SELECT * FROM payments WHERE gateway='mpesa' AND status='pending' AND metadata LIKE ?");
    $stmt->execute(['%'.$checkoutId.'%']);
    $payment=$stmt->fetch();
    if($payment){
        $db->prepare("UPDATE payments SET status='completed',transaction_id=?,metadata=? WHERE id=?")
           ->execute([$txId,json_encode(['mpesa_receipt'=>$txId,'phone'=>$phone]),$payment['id']]);
        // Activate membership
        activatePurchasedPlan($db, $payment['user_id'], $payment['plan']);
    }
}
