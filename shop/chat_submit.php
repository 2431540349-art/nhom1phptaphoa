<?php
require_once __DIR__ . '/Admin/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit();
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $message === '') {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json'); echo json_encode(['ok'=>false,'message'=>'Vui lòng nhập tên và tin nhắn.']); exit();
    }
    $_SESSION['chat_error'] = 'Vui lòng nhập tên và tin nhắn.';
    header('Location: index.php'); exit();
}

$stmt = $conn->prepare("INSERT INTO customer_messages (customer_name, phone, message) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param('sss', $name, $phone, $message);
    $stmt->execute();
    $stmt->close();
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json'); echo json_encode(['ok'=>true,'message'=>'Tin nhắn đã gửi. Chúng tôi sẽ phản hồi sớm.']); exit();
    }
    $_SESSION['chat_success'] = 'Tin nhắn đã gửi. Chúng tôi sẽ phản hồi sớm.';
} else {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json'); echo json_encode(['ok'=>false,'message'=>'Lỗi khi gửi tin nhắn.']); exit();
    }
    $_SESSION['chat_error'] = 'Lỗi khi gửi tin nhắn.';
}

header('Location: index.php');
exit();
