<?php
require_once __DIR__ . '/config.php';

function onepay_post($path, $payload){
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_api("onepay_post $path", ['url'=>$url,'payload'=>$payload,'http'=>$http,'err'=>$err,'res'=>$res]);

    if($err) return ['status'=>0,'error'=>'curl_error: '.$err];

    $decoded = json_decode($res, true);
    if($decoded === null){
        return ['status'=>0,'error'=>'invalid_json_response','raw'=>$res];
    }
    return $decoded;
}

function onepay_get($path, $params = []){
    global $ONEPAY_BASE, $ONEPAY_TOKEN;
    $url = rtrim($ONEPAY_BASE, '/') . '/' . ltrim($path, '/');
    if(!empty($params)) $url .= '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $ONEPAY_TOKEN",
        "Content-Type: application/json",
        "User-Agent: ONEPAY/1.0",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_api("onepay_get $path", ['url'=>$url,'params'=>$params,'http'=>$http,'err'=>$err,'res'=>$res]);

    if($err) return ['status'=>0,'error'=>'curl_error: '.$err];
    $decoded = json_decode($res, true);
    if($decoded === null) return ['status'=>0,'error'=>'invalid_json_response','raw'=>$res];
    return $decoded;
}
