<?php
session_start();
require_once(__DIR__ . '/../Admin/config.php');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<p>Sản phẩm không hợp lệ.</p>";
    exit;
}
$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows == 0) {
    echo "<p>Sản phẩm không tồn tại.</p>";
    exit;
}
$product = $res->fetch_assoc();
$stmt->close();

$user_id = intval($_SESSION['user_id'] ?? 0);
if ($user_id > 0) {
    $check = $conn->prepare("SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ? LIMIT 1");
    if (!$check) {
        error_log('Prepare failed (check carts): ' . $conn->error);
        echo "<p>Lỗi hệ thống (prepare). Vui lòng kiểm tra logs.</p>";
        exit;
    }
    $check->bind_param('ii', $user_id, $id);
    $check->execute();
    $cres = $check->get_result();
    $now = date('Y-m-d H:i:s');

    if ($cres && $cres->num_rows > 0) {
        $crow = $cres->fetch_assoc();
        $upd = $conn->prepare("UPDATE carts SET quantity = quantity + 1, added_at = ? WHERE id = ?");
        $upd->bind_param('si', $now, $crow['id']);
        $upd->execute();
        $upd->close();
    } else {
        $ins = $conn->prepare("INSERT INTO carts (user_id, product_id, price_at_add, quantity, added_at) VALUES (?, ?, ?, ?, ?)");
        if (!$ins) {
            error_log('Prepare failed (insert carts): ' . $conn->error);
            echo "<p>Lỗi hệ thống (prepare insert). Vui lòng kiểm tra logs.</p>";
            exit;
        }
        $price = (float)$product['price'];
        $qty = 1;
        $ins->bind_param('iidis', $user_id, $id, $price, $qty, $now);
        if (!$ins->execute()) {
            error_log('Execute failed (insert carts): ' . $ins->error);
            echo "<p>Lỗi khi thêm vào giỏ (execute). Vui lòng kiểm tra logs.</p>";
        }
        $ins->close();
    }
    $check->close();

    $redirect = $_GET['redirect'] ?? '../index.php';
    header("Location: $redirect");
    exit;
} else {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name'  => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'qty'   => 1
        ];
    }

    $redirect = $_GET['redirect'] ?? '../index.php';
    header("Location: $redirect");
    exit;
}
?>
