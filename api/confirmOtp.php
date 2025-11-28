<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

$input = json_decode(file_get_contents('php://input'), true);
$order_id = trim($input['order_id'] ?? '');
$otp = trim($input['otp'] ?? '');

if($order_id === '' || $otp === ''){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'invalid_input']);
    exit;
}

// افترض أن endpoint التحقق في OnePay هو confirm (عدل إن اختلف)
$payload = [
    'order_id' => $order_id,
    'otp' => $otp
];

$response = onepay_post('confirm', $payload);

if(isset($response['status']) && $response['status']==1){
    echo json_encode(['status'=>1,'message'=>'confirmed','raw'=>$response], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status'=>0,'error'=>$response['error'] ?? 'confirm_failed','raw'=>$response], JSON_UNESCAPED_UNICODE);
}
