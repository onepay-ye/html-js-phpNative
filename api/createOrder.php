<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/api_client.php';

// قراءة JSON من الـ body
$input = json_decode(file_get_contents('php://input'), true);
if(!$input){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'bad_request']);
    exit;
}

// تحقق بسيط من المدخلات
$payerPhone = trim($input['payerPhone'] ?? '');
$amount = floatval($input['amount'] ?? 0);
$currency = $input['currency'] ?? 'YER';
$description = $input['description'] ?? '';

if($payerPhone === '' || $amount <= 0){
    http_response_code(400);
    echo json_encode(['status'=>0,'error'=>'invalid_input']);
    exit;
}

// جهّز payload بحسب واجهة OnePay (تعديل حسب الـ API الفعلي)
$payload = [
    "payment_name" => "cashpay",
    "currency_id" => $currency,
    "payerPhone" => $payerPhone,
    "payerEmail" => $input['payerEmail'] ?? '',
    "des" => $description,
    "beneficiaryList" => [
        [
            "amount" => $amount,
            "note" => $description ?: "Payment"
        ]
    ]
];

$response = onepay_post('createOrder', $payload);

// يُتوقع أن OnePay يُرجع order_id أو شيء مشابه
if(isset($response['status']) && $response['status']==1){
    // مثال: فرضاً OnePay ترجع order_id
    echo json_encode([
        'status'=>1,
        'order_id'=> $response['data']['order_id'] ?? ($response['order_id'] ?? null),
        'raw' => $response
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status'=>0,'error'=>$response['error'] ?? 'onepay_error','raw'=>$response], JSON_UNESCAPED_UNICODE);
}
