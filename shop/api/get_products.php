<?php
header('Content-Type: application/json; charset=utf-8');
if (!isset($_GET['ids']) || trim($_GET['ids']) === '') {
    echo json_encode([]);
    exit;
}
require_once __DIR__ . '/../Admin/config.php';
$raw = trim($_GET['ids']);
$parts = array_filter(array_map('trim', explode(',', $raw)), function($v){ return $v !== ''; });
$ids = array_map('intval', $parts);
$ids = array_filter($ids, function($v){ return $v > 0; });
if (empty($ids)) { echo json_encode([]); exit; }
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, name, price, image, description FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($r = $res->fetch_assoc()) {
    $out[] = $r;
}
$stmt->close();
echo json_encode($out);
