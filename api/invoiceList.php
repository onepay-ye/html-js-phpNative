<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/api_client.php";

/**
 * Required:
 * email (GET)
 */

if (!isset($_GET["email"])) {
    echo json_encode(["status" => 0, "error" => "email is required"]);
    exit;
}

$email = trim($_GET["email"]);

// API endpoint:
// GET /cashpay/invoice/list/{email}
// wallet type always comes from JSON tester (POST request)

$wallet = $_GET["wallet"] ?? "cashpay"; // fallback

$endpoint = "{$wallet}/invoice/list/" . urlencode($email);

$response = onepay_get($endpoint);

echo json_encode($response, JSON_UNESCAPED_UNICODE);
