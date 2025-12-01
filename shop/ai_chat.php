<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/ai_config.php';

$cfg = get_ai_config();
$provider = isset($cfg['provider']) ? $cfg['provider'] : 'google';
$openai_key = isset($cfg['openai_api_key']) ? $cfg['openai_api_key'] : '';
$openai_model = isset($cfg['openai_model']) ? $cfg['openai_model'] : 'gpt-3.5-turbo';
$apiKey = isset($cfg['google_api_key']) ? $cfg['google_api_key'] : '';
$model = isset($cfg['google_model']) ? $cfg['google_model'] : '';
$endpointBase = rtrim(isset($cfg['google_endpoint']) ? $cfg['google_endpoint'] : 'https://generativelanguage.googleapis.com/v1/models', '/');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'message' => 'Only POST allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$message = '';
if (is_array($input) && isset($input['message'])) {
    $message = trim($input['message']);
}
if ($message === '') {
    echo json_encode(['ok' => false, 'message' => 'Empty message']);
    exit;
}

if ($provider === 'openai') {
} else {
    if (empty($apiKey)) {
        echo json_encode(['ok' => false, 'message' => 'AI configuration not set on server']);
        exit;
    }
}

if ($provider === 'openai') {
    if (empty($openai_key)) {
        echo json_encode(['ok' => false, 'message' => 'OpenAI API key not configured on server']);
        exit;
    }

    $url = 'https://api.openai.com/v1/chat/completions';
    $payload = [
        'model' => $openai_model,
        'messages' => [
            ['role' => 'system', 'content' => 'Bạn là trợ lý bán hàng thân thiện, ngắn gọn, và hữu ích. Trả lời bằng tiếng Việt khi người dùng dùng tiếng Việt.'],
            ['role' => 'user', 'content' => $message]
        ],
        'temperature' => 0.2,
        'max_tokens' => 200
    ];

    $openai_keys = [$openai_key];
    $localCfgFile = __DIR__ . '/ai_config.local.php';
    if (file_exists($localCfgFile)) {
        try {
            $local = include $localCfgFile;
            if (is_array($local)) {
                if (!empty($local['openai_api_key'])) $openai_keys[] = $local['openai_api_key'];
                if (!empty($local['openai_api_keys']) && is_array($local['openai_api_keys'])) {
                    foreach ($local['openai_api_keys'] as $k) if ($k) $openai_keys[] = $k;
                }
            }
        } catch (Exception $e) {
            @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] Failed to load ai_config.local.php: %s\n", date('c'), $e->getMessage()), FILE_APPEND | LOCK_EX);
        }
    }

    $resp = false; $err = ''; $code = 0; $json = null; $usedKey = null;
    foreach ($openai_keys as $candidateKey) {
        if (empty($candidateKey)) continue;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $candidateKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $snippet = is_string($resp) ? substr($resp, 0, 2000) : '';
        @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] OpenAI call try key=%s http=%s err=%s resp_snippet=%s\n", date('c'), substr($candidateKey,0,8).'...', $code, $err, $snippet), FILE_APPEND | LOCK_EX);

        if ($resp !== false && $code < 400) {
            $json = json_decode($resp, true);
            $usedKey = $candidateKey;
            break;
        }
    }

    if ($resp === false || $code >= 400) {
        $snippet = is_string($resp) ? substr($resp, 0, 1000) : '';
        if ($code === 429) {
            $suggestions = [
                'Kiểm tra Billing/Quota trên OpenAI dashboard',
                'Tạo API key khác và thêm vào file ai_config.local.php dưới openai_api_keys',
                'Sử dụng model rẻ hơn hoặc giảm tần suất/gói token',
                'Hoặc chuyển sang provider khác (ví dụ OpenAI với key mới)'
            ];
            @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] OpenAI quota exhausted after trying %d keys. snippet=%s\n", date('c'), count($openai_keys), $snippet), FILE_APPEND | LOCK_EX);
            $localAnswer = null;
            $faqFile = __DIR__ . '/ai_faq.php';
            if (file_exists($faqFile)) {
                try {
                    include_once $faqFile;
                    if (function_exists('find_local_faq_answer')) {
                        $localAnswer = find_local_faq_answer($message);
                    }
                } catch (Exception $e) {
                    @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] Failed to load ai_faq.php: %s\n", date('c'), $e->getMessage()), FILE_APPEND | LOCK_EX);
                }
            }
            if ($localAnswer) {
                echo json_encode(['ok' => true, 'reply' => $localAnswer, 'raw' => ['source' => 'local_faq']]);
                exit;
            }

            $fallback = "Xin lỗi, hệ thống trợ lý AI hiện đang vượt hạn mức sử dụng. Bạn có thể: (1) kiểm tra Billing trên OpenAI, (2) cung cấp API key khác, hoặc (3) thử lại sau. Sau đây là một vài gợi ý nhanh:\n- Hỏi về giờ mở cửa, địa chỉ cửa hàng, phương thức thanh toán hỗ trợ.\n- Nếu cần giúp với đơn hàng, cung cấp mã đơn hoặc mô tả ngắn về vấn đề.";
            echo json_encode(['ok' => false, 'message' => 'Hết hạn mức (quota) OpenAI: vui lòng kiểm tra billing hoặc sử dụng API key khác.', 'http_code' => $code, 'error' => $err, 'raw' => $snippet, 'suggestions' => $suggestions, 'fallback' => $fallback]);
            exit;
        }
        $msg = 'OpenAI service error';
        if ($code) $msg .= ' (HTTP ' . intval($code) . ')';
        echo json_encode(['ok' => false, 'message' => $msg, 'http_code' => $code, 'error' => $err, 'raw' => $snippet]);
        exit;
    }

    $json = json_decode($resp, true);
    $reply = '';
    if (is_array($json) && isset($json['choices'][0]['message']['content'])) {
        $reply = $json['choices'][0]['message']['content'];
    } elseif (is_array($json) && isset($json['choices'][0]['text'])) {
        $reply = $json['choices'][0]['text'];
    } else {
        $reply = json_encode($json);
    }

    echo json_encode(['ok' => true, 'reply' => $reply, 'raw' => $json, 'used' => ['provider'=>'openai','model'=>$openai_model]]);
    exit;
}

$candidateModels = array_filter(array_unique(array_merge([$model], [
    'models/gemini-1.5',
    'models/gemini-1.0',
    'models/gemini-flash',
    'models/text-bison-001',
    'models/text-bison-002'
] )));

$candidateEndpoints = [
    'https://generativelanguage.googleapis.com/v1/models',
    'https://generativelanguage.googleapis.com/v1beta2/models',
    'https://generativelanguage.googleapis.com/v1beta3/models'
];

$resp = false; $err = ''; $code = 0; $json = null; $used = null;
foreach ($candidateEndpoints as $ep) {
    foreach ($candidateModels as $candModel) {
        if (empty($candModel)) continue;
        $url = rtrim($ep, '/') . '/' . $candModel . ':generateText?key=' . urlencode($apiKey);
        $payload = [
            'prompt' => ['text' => $message],
            'maxOutputTokens' => 400,
            'temperature' => 0.2
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $snippet = is_string($resp) ? substr($resp, 0, 2000) : '';
        $logLine = sprintf("[%s] AI try model=%s endpoint=%s http=%s curl_err=%s resp_snippet=%s\n", date('c'), $candModel, $ep, $code, $err, $snippet);
        @file_put_contents(__DIR__ . '/ai_debug.log', $logLine, FILE_APPEND | LOCK_EX);

        if ($resp !== false && $code < 400) {
            $json = json_decode($resp, true);
            $used = ['model'=>$candModel,'endpoint'=>$ep,'http'=>$code];
            break 2;
        }
    }
}

if ($resp === false || $code >= 400) {
    $snippet = is_string($resp) ? substr($resp, 0, 1000) : '';
    $msg = 'AI service error';
    if ($code) $msg .= ' (HTTP ' . intval($code) . ')';
    echo json_encode(['ok' => false, 'message' => $msg, 'http_code' => $code, 'error' => $err, 'raw' => $snippet]);
    exit;
}

$reply = '';
if (is_array($json)) {
    if (isset($json['candidates']) && is_array($json['candidates']) && isset($json['candidates'][0]['output'])) {
        $reply = $json['candidates'][0]['output'];
    } elseif (isset($json['candidates']) && is_array($json['candidates']) && isset($json['candidates'][0]['content'])) {
        $c = $json['candidates'][0]['content'];
        if (is_array($c)) {
            $parts = [];
            array_walk_recursive($c, function($v) use (&$parts){ if (is_string($v)) $parts[] = $v; });
            $reply = implode("\n", $parts);
        }
    } elseif (isset($json['output']) && is_string($json['output'])) {
        $reply = $json['output'];
    } elseif (isset($json['result']) && is_string($json['result'])) {
        $reply = $json['result'];
    }
}

if ($reply === '') {
    $reply = isset($json['candidates'][0]) ? (is_string($json['candidates'][0]) ? $json['candidates'][0] : json_encode($json['candidates'][0])) : json_encode($json);
}

if (!empty($used) && isset($used['model']) && isset($used['endpoint'])) {
    $cfgFile = __DIR__ . '/ai_config.php';
    try {
        if (is_writable($cfgFile)) {
            $currentCfg = @file_get_contents($cfgFile);
            if ($currentCfg !== false) {
                $api_key_val = $apiKey;
                $newContent = "<?php\n// AI configuration - store your API key and model here.\n// WARNING: keep this file private on your server and do not commit to public repositories.\nfunction get_ai_config(){\n    return [\n        // Provided by user (keep secret)\n        'api_key' => '" . str_replace("'", "\\'", $api_key_val) . "',\n        // Model name - automatically detected\n        'model' => '" . addslashes($used['model']) . "',\n        // Use the generative language endpoint base.\n        'endpoint' => '" . rtrim($used['endpoint'], '/') . "'\n    ];\n}\n";
                @copy($cfgFile, $cfgFile . '.bak.' . time());
                @file_put_contents($cfgFile, $newContent, LOCK_EX);
                @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] ai_config.php updated to model=%s endpoint=%s\n", date('c'), $used['model'], $used['endpoint']), FILE_APPEND | LOCK_EX);
            }
        }
    } catch (Exception $ex) {
        @file_put_contents(__DIR__ . '/ai_debug.log', sprintf("[%s] Failed to persist ai_config: %s\n", date('c'), $ex->getMessage()), FILE_APPEND | LOCK_EX);
    }
}

echo json_encode(['ok' => true, 'reply' => $reply, 'raw' => $json, 'used' => $used]);
