<?php
require_once __DIR__ . '/config.php';

/****************************************************
 * READ TOKEN FROM HEADER
 ****************************************************/
function getAuthorizationToken() {
    $headers = [];

    // Try standard getallheaders()
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    }

    // Support Apache / Nginx alternative header names
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
    }

    // Support FastCGI / PHP Built-in server
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!isset($headers['Authorization'])) {
        return null;
    }

    $auth = trim($headers['Authorization']);

    if (stripos($auth, 'Bearer ') === 0) {
        return substr($auth, 7);
    }

    return null;
}

//print_r(getallheaders());


$ONEPAY_TOKEN = getAuthorizationToken();

if (!$ONEPAY_TOKEN) {
    echo json_encode([
        "status" => 0,
        "error" => "Missing Authorization token (Bearer Token). Please enter your API Token in the UI."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


/****************************************************
 * POST REQUEST
 ****************************************************/
function onepay_post($path, $payload)
{
    global $ONEPAY_BASE, $ONEPAY_TOKEN;

    $url = rtrim($ONEPAY_BASE, '/') . '/' . ltrim($path, '/');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $ONEPAY_TOKEN",
        "Content-Type: application/json",
        "User-Agent: ONEPAY/1.0",
        "Accept: application/json"
    ]);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));

    // SSL Fix for localhost
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_api("onepay_post $path", [
        'url' => $url,
        'payload' => $payload,
        'http' => $http,
        'err' => $err,
        'res' => $res
    ]);

    if ($err) {
        return ['status' => 0, 'error' => 'curl_error: ' . $err];
    }

    $decoded = json_decode($res, true);
    if ($decoded === null) {
        return ['status' => 0, 'error' => 'invalid_json_response', 'raw' => $res];
    }

    return $decoded;
}


/****************************************************
 * GET REQUEST
 ****************************************************/
function onepay_get($path, $params = [])
{
    global $ONEPAY_BASE, $ONEPAY_TOKEN;

    $url = rtrim($ONEPAY_BASE, '/') . '/' . ltrim($path, '/');

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $ONEPAY_TOKEN",
        "Content-Type: application/json",
        "User-Agent: ONEPAY/1.0",
        "Accept: application/json"
    ]);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    log_api("onepay_get $path", [
        'url' => $url,
        'params' => $params,
        'http' => $http,
        'err' => $err,
        'res' => $res
    ]);

    if ($err) {
        return ['status' => 0, 'error' => 'curl_error: ' . $err];
    }

    $decoded = json_decode($res, true);
    if ($decoded === null) {
        return ['status' => 0, 'error' => 'invalid_json_response', 'raw' => $res];
    }

    return $decoded;
}
