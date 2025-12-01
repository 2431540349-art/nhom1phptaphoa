<?php
session_start();
require_once __DIR__ . '/../Admin/config.php';
header('Content-Type: application/json; charset=utf-8');

$user_id = intval($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
    echo json_encode(['ok' => false, 'favorites' => []]);
    exit;
}

$stmt = $conn->prepare("SELECT product_id FROM favorites WHERE user_id = ? ORDER BY added_at DESC");
if (!$stmt) {
    echo json_encode(['ok' => false, 'favorites' => []]);
    exit;
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$ids = [];
while ($r = $res->fetch_assoc()) {
    $ids[] = intval($r['product_id']);
}
$stmt->close();

echo json_encode(['ok' => true, 'favorites' => $ids]);
?>