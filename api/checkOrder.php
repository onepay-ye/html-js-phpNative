<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'bad_request']);
    exit;
}

$payment_name = $input['payment_name'] ?? '';
$orderID = $input['orderID'] ?? '';

if($payment_name === '' || $orderID === ''){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'missing_fields']);
    exit;
}

$payload = $input;

$response = onepay_post('checkorder', $payload);

if(isset($response['status'])){
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status'=>0,'error'=>'onepay_no_response','raw'=>$response], JSON_UNESCAPED_UNICODE);
}
