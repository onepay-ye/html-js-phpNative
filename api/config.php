<?php
// config.php
// load simple .env key=value
$env_path = __DIR__ . "/../.env";
$config = [];

if(file_exists($env_path)){
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line){
        if(trim($line)==='' || strpos(trim($line),'#')===0) continue;
        [$k,$v] = array_map('trim', explode('=', $line, 2) + [1 => null]);
        if($k) $config[$k] = $v;
    }
}

//$ONEPAY_TOKEN = $config['ONEPAY_TOKEN'] ?? '';
$ONEPAY_BASE = $config['ONEPAY_BASE_URL'] ?? 'https://one-pay.info/api/v2';
$LOG_DIR = __DIR__ . '/logs';
if(!is_dir($LOG_DIR)) mkdir($LOG_DIR, 0755, true);

function log_api($name, $data){
    global $LOG_DIR;
    $fn = $LOG_DIR . '/' . date('Y-m-d') . ".log";
    $line = "[".date('Y-m-d H:i:s')."] $name: " . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    file_put_contents($fn, $line, FILE_APPEND | LOCK_EX);
}

//if(empty($ONEPAY_TOKEN)){
//    http_response_code(500);
//    echo json_encode(['status'=>0,'error'=>'ONEPAY_TOKEN not set in .env']);
//    exit;
//}
