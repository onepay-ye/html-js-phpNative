<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

$order_id = trim($_GET['order_id'] ?? '');
if($order_id === ''){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'missing_order_id']);
    exit;
}

// افترض endpoint checkOrder
$response = onepay_get('checkOrder', ['order_id' => $order_id]);

if(isset($response['status'])){
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status'=>0,'error'=>'onepay_no_response','raw'=>$response], JSON_UNESCAPED_UNICODE);
}
