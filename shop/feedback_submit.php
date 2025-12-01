<?php
session_start();
require_once __DIR__ . '/Admin/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$product_id = isset($_POST['product_id']) && is_numeric($_POST['product_id']) ? intval($_POST['product_id']) : null;
$rating = isset($_POST['rating']) && is_numeric($_POST['rating']) ? intval($_POST['rating']) : null;

if ($name === '' || $message === '') {
    $_SESSION['feedback_error'] = 'Vui lòng cung cấp ít nhất tên và nội dung.';
    header('Location: index.php'); exit();
}

if ($product_id) {
    $user_id = intval($_SESSION['user_id'] ?? 0);
    if ($user_id > 0) {
        $purchaseCheck = $conn->prepare("
            SELECT oi.id FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.id_user = ? AND oi.product_id = ?
            LIMIT 1
        ");
        if ($purchaseCheck) {
            $purchaseCheck->bind_param('ii', $user_id, $product_id);
            $purchaseCheck->execute();
            $pRes = $purchaseCheck->get_result();
            if (!$pRes || $pRes->num_rows == 0) {
                $_SESSION['feedback_error'] = 'Bạn chỉ có thể đánh giá những sản phẩm đã mua.';
                $purchaseCheck->close();
                header('Location: index.php?page=3&id=' . $product_id);
                exit();
            }
            $purchaseCheck->close();
        }
    }
}

$create = "CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(191) NOT NULL,
    email VARCHAR(191) DEFAULT NULL,
    message TEXT NOT NULL,
    product_id INT DEFAULT NULL,
    rating TINYINT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    status ENUM('new','published','archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($create);
$tbl = $conn->query("SHOW TABLES LIKE 'feedbacks'");
if ($tbl && $tbl->num_rows > 0) {
    $c = $conn->query("SHOW COLUMNS FROM feedbacks LIKE 'product_id'");
    if (!$c || $c->num_rows == 0) {
        @$conn->query("ALTER TABLE feedbacks ADD COLUMN product_id INT DEFAULT NULL");
    }
    $c2 = $conn->query("SHOW COLUMNS FROM feedbacks LIKE 'rating'");
    if (!$c2 || $c2->num_rows == 0) {
        @$conn->query("ALTER TABLE feedbacks ADD COLUMN rating TINYINT DEFAULT NULL");
    }
    $c3 = $conn->query("SHOW COLUMNS FROM feedbacks LIKE 'user_id'");
    if (!$c3 || $c3->num_rows == 0) {
        @$conn->query("ALTER TABLE feedbacks ADD COLUMN user_id INT DEFAULT NULL");
    }
}

if ($rating !== null) {
    if ($rating < 1) $rating = 1; if ($rating > 5) $rating = 5;
}

$status = $product_id ? 'published' : 'new';
$user_id = intval($_SESSION['user_id'] ?? 0);

$stmt = $conn->prepare("INSERT INTO feedbacks (customer_name, email, message, product_id, rating, user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?) ");
if ($stmt) {
    $stmt->bind_param('ssissis', $name, $email, $message, $product_id, $rating, $user_id, $status);
    $stmt->execute();
    $stmt->close();
    if ($product_id) {
        header('Location: index.php?page=3&id=' . $product_id);
        exit();
    }
    $_SESSION['feedback_success'] = 'Cảm ơn bạn! Phản hồi đã được gửi.';
} else {
    $_SESSION['feedback_error'] = 'Lỗi khi lưu phản hồi.';
}

header('Location: index.php');
exit();
