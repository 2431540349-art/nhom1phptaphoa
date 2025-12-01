<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Admin/config.php';

$name = trim($_GET['name'] ?? '');
$phone = trim($_GET['phone'] ?? '');
if ($name === '' && $phone === '') { echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT id, customer_name, phone, sender, message, status, created_at FROM customer_messages WHERE customer_name = ? AND phone = ? ORDER BY created_at ASC");
if (!$stmt) { echo json_encode([]); exit; }
$stmt->bind_param('ss', $name, $phone);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($r = $res->fetch_assoc()) {
    $out[] = $r;
}
$stmt->close();

echo json_encode($out);
