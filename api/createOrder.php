<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/api_client.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["status" => 0, "error" => "Invalid JSON"]);
    exit;
}

// Forward SAME BODY as Postman without any changes
$response = onepay_post("createorder", $input);

// Parse returned orderID
echo json_encode($response, JSON_UNESCAPED_UNICODE);
