<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/ai_config.php';
$cfg = get_ai_config();
$apiKey = isset($cfg['api_key']) ? $cfg['api_key'] : '';
$endpointBase = rtrim(isset($cfg['endpoint']) ? $cfg['endpoint'] : 'https://generativelanguage.googleapis.com/v1beta2/models', '/');
if (empty($apiKey)) {
    echo json_encode(['ok'=>false,'message'=>'No API key configured']);
    exit;
}

$url = $endpointBase . '?key=' . urlencode($apiKey);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$resp = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($resp === false || $code >= 400) {
    $snippet = is_string($resp) ? substr($resp,0,2000) : '';
    $logLine = sprintf("[%s] AI models list failed: http=%s curl_err=%s resp=%s\n", date('c'), $code, $err, $snippet);
    @file_put_contents(__DIR__ . '/ai_debug.log', $logLine, FILE_APPEND | LOCK_EX);
    echo json_encode(['ok'=>false,'http_code'=>$code,'error'=>$err,'raw'=>$snippet]);
    exit;
}

echo $resp;
