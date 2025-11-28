<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

$input = json_decode(file_get_contents('php://input'), true);
if(!$input){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'bad_request']);
    exit;
}

$payerPhone = trim($input['payerPhone'] ?? '');
$amount = floatval($input['beneficiaryList'][0]['amount'] ?? 0);
$currency = $input['currency_id'] ?? 'YER';
$description = $input['des'] ?? '';

if($payerPhone === '' || $amount <= 0){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'invalid_input']);
    exit;
}

$payload = $input; // forward as-is based on Postman body

$response = onepay_post('createorder', $payload);

if(isset($response['status']) && $response['status']==1){
    echo json_encode([
        'status'=>1,
        'order_id'=> $response['orderID'] ?? ($response['data']['orderID'] ?? ($response['data']['order_id'] ?? null)),
        'raw' => $response
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status'=>0,'error'=>$response['error'] ?? 'onepay_error','raw'=>$response], JSON_UNESCAPED_UNICODE);
}
