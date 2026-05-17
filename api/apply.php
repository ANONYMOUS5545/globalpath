<?php
require_once '../includes/config.php';
require_once '../includes/content_bootstrap.php';
header('Content-Type: application/json');
startSecureSession();

if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit; }
if(!isLoggedIn()){ echo json_encode(['success'=>false,'message'=>'Please log in to apply.']); exit; }

$input = json_decode(file_get_contents('php://input'),true);
if(!validateCSRF($input['csrf']??'')){ echo json_encode(['success'=>false,'message'=>'Security error.']); exit; }

$type = in_array($input['type']??'',['scholarship','job','visa']) ? $input['type'] : null;
$refId = (int)($input['id']??0);

if(!$type||!$refId){ echo json_encode(['success'=>false,'message'=>'Invalid request.']); exit; }

$user = getCurrentUser();
$db = getDB();
bootSiteContent($db);

// Check membership for jobs
if($type==='job' && $user['membership_type']==='free'){
    echo json_encode(['success'=>false,'message'=>'Premium membership required to apply for jobs. Please upgrade your plan.']); exit;
}

// Check duplicate
$chk=$db->prepare("SELECT id FROM applications WHERE user_id=? AND type=? AND reference_id=?");
$chk->execute([$user['id'],$type,$refId]);
if($chk->fetch()){ echo json_encode(['success'=>false,'message'=>'You have already applied for this opportunity.']); exit; }

// Verify item exists
$table = $type==='job'?'jobs':'scholarships';
if($type==='visa') $table='visas';
$v=$db->prepare("SELECT id FROM $table WHERE id=? AND is_active=1");
$v->execute([$refId]);
if(!$v->fetch()){ echo json_encode(['success'=>false,'message'=>'This opportunity is no longer available.']); exit; }

if ($type === 'job') {
    $jobStmt = $db->prepare("SELECT * FROM jobs WHERE id = ? AND is_active = 1");
    $jobStmt->execute([$refId]);
    $job = $jobStmt->fetch();

    if (!$job) {
        echo json_encode(['success'=>false,'message'=>'This opportunity is no longer available.']); exit;
    }

    if (!userCanAccessJob($user, $job)) {
        echo json_encode(['success'=>false,'message'=>'Your current membership tier cannot apply for this listing.']); exit;
    }

    if (hasJobDeadlinePassed($job)) {
        echo json_encode(['success'=>false,'message'=>'The deadline for this job has already passed.']); exit;
    }
}

// Insert
$db->prepare("INSERT INTO applications (user_id,type,reference_id) VALUES (?,?,?)")
   ->execute([$user['id'],$type,$refId]);
echo json_encode(['success'=>true,'message'=>'Application submitted successfully!']);
