<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/api_client.php";

// Support both POST JSON body and GET ?wallet=...
$input = json_decode(file_get_contents("php://input"), true);

// prefer POST body payment_name, fallback to GET param 'wallet'
$wallet = null;
if (is_array($input) && !empty($input['payment_name'])) {
    $wallet = $input['payment_name'];
} elseif (isset($_GET['wallet']) && $_GET['wallet'] !== '') {
    $wallet = $_GET['wallet'];
}

if (!$wallet) {
    http_response_code(400);
    echo json_encode(['status' => 0, 'error' => 'payment_name (wallet) is required']);
    exit;
}

// call OnePay endpoint: GET /{wallet}/accountinfo
$endpoint = rtrim($wallet, '/') . '/accountinfo';

// onepay_get will call: {ONEPAY_BASE}/{endpoint}
$response = onepay_get($endpoint);

// return the OnePay response as-is (or wrap if you prefer)
echo json_encode($response, JSON_UNESCAPED_UNICODE);
