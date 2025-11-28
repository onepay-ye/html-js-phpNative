<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/api_client.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["status" => 0, "error" => "Invalid JSON"]);
    exit;
}

// Must be POST with exact JSON structure
$response = onepay_post("checkorder", $input);

echo json_encode($response, JSON_UNESCAPED_UNICODE);
