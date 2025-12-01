<?php
require_once __DIR__ . '/partials/helpers.php';
session_start();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: index.php?page=4'); exit();
}
$total = 0;
foreach ($cart as $it) { $qty = intval($it['qty'] ?? 1); $total += $qty * floatval($it['price']); }

$buyer_name = trim($_POST['name'] ?? '');
$buyer_phone = trim($_POST['phone'] ?? '');
$buyer_address = trim($_POST['address'] ?? '');
$login_user_id = intval($_SESSION['user_id'] ?? 0);

if ($buyer_name === '' || $buyer_phone === '') {
    $_SESSION['checkout_error'] = 'Vui lòng nhập tên và số điện thoại';
    header('Location: index.php?page=5'); exit();
}
require_once __DIR__ . '/Admin/config.php';
try {
    $conn->begin_transaction();
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, address) VALUES (?,?,?)");
    $stmt->bind_param('sss', $buyer_name, $buyer_phone, $buyer_address);
    $stmt->execute();
    $customer_id = $conn->insert_id;
    $stmt->close();

    $status = 'pending_payment';
    $order_date = date('Y-m-d H:i:s');
    if (!empty($login_user_id) && $login_user_id > 0) {
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, total_amount, status, order_date, id_user) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('isdssi', $customer_id, $buyer_name, $total, $status, $order_date, $login_user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, total_amount, status, order_date) VALUES (?,?,?,?,?)");
        $stmt->bind_param('isdss', $customer_id, $buyer_name, $total, $status, $order_date);
    }
    $stmt->execute();
    $new_order_id = $conn->insert_id;
    $stmt->close();

    $stmtWithPid = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) VALUES (?,?,?,?,?,?)");
    $stmtNoPid = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price, subtotal) VALUES (?,?,?,?,?)");

    foreach ($cart as $product_id => $item) {
        $qty = intval($item['qty'] ?? 1);
        $name = $item['name'];
        $price = floatval($item['price']);
        $subtotal = $qty * $price;
        $pid = is_numeric($product_id) ? intval($product_id) : 0;
        $pid_exists = false;
        if ($pid > 0) {
            $res = $conn->query("SELECT id FROM products WHERE id = " . $pid . " LIMIT 1");
            if ($res && $res->num_rows > 0) $pid_exists = true;
        }
        if ($pid_exists) {
            $stmtWithPid->bind_param('iisidd', $new_order_id, $pid, $name, $qty, $price, $subtotal);
            $stmtWithPid->execute();
        } else {
            $stmtNoPid->bind_param('isidd', $new_order_id, $name, $qty, $price, $subtotal);
            $stmtNoPid->execute();
        }
    }

    if ($stmtWithPid) $stmtWithPid->close();
    if ($stmtNoPid) $stmtNoPid->close();

    $conn->commit();


} catch (Exception $e) {
    if ($conn) $conn->rollback();
    $_SESSION['checkout_error'] = 'Lỗi tạo đơn hàng: ' . $e->getMessage();
    header('Location: index.php?page=5'); exit();
}

$amount = $total; 
$txn_ref = $new_order_id; 

header('Location: vnpay_php/vnpay_pay.php?amount=' . urlencode($amount) . '&txn_ref=' . urlencode($txn_ref));
exit();
