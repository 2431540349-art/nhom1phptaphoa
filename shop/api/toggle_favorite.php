<?php
session_start();
require_once __DIR__ . '/../Admin/config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = intval($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = intval($input['product_id'] ?? ($_POST['product_id'] ?? 0));
if ($product_id <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Invalid product id']);
    exit;
}

$check = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1");
if (!$check) {
    echo json_encode(['ok' => false, 'message' => 'Prepare failed', 'error' => $conn->error]);
    exit;
}
$check->bind_param('ii', $user_id, $product_id);
$check->execute();
$res = $check->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $fid = intval($row['id']);
    $del = $conn->prepare("DELETE FROM favorites WHERE id = ?");
    if ($del) {
        $del->bind_param('i', $fid);
        $ok = $del->execute();
        $del->close();
        echo json_encode(['ok' => (bool)$ok, 'favorited' => false]);
        $check->close();
        exit;
    } else {
        echo json_encode(['ok' => false, 'message' => 'Prepare delete failed', 'error' => $conn->error]);
        $check->close();
        exit;
    }
} else {
    $now = date('Y-m-d H:i:s');
    $ins = $conn->prepare("INSERT INTO favorites (user_id, product_id, added_at) VALUES (?, ?, ?)");
    if (!$ins) {
        echo json_encode(['ok' => false, 'message' => 'Prepare insert failed', 'error' => $conn->error]);
        $check->close();
        exit;
    }
    $ins->bind_param('iis', $user_id, $product_id, $now);
    $ok = $ins->execute();
    $ins->close();
    echo json_encode(['ok' => (bool)$ok, 'favorited' => true]);
    $check->close();
    exit;
}

?>