<?php
$cart = $_SESSION['cart'] ?? [];
$total = 0;
$error_message = '';

$selected_ids = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_ids = isset($_POST['selected']) && is_array($_POST['selected']) ? array_map('intval', $_POST['selected']) : [];
} else {
    $selected_ids = isset($_GET['selected']) && is_array($_GET['selected']) ? array_map('intval', $_GET['selected']) : [];
}

$cart_for_checkout = [];
if (!empty($selected_ids)) {
    foreach ($selected_ids as $sid) {
        if (isset($cart[$sid])) $cart_for_checkout[$sid] = $cart[$sid];
    }
} else {
    $cart_for_checkout = $cart; 
}

foreach ($cart_for_checkout as $item) {
    $qty = $item['qty'] ?? 1;
    $total += $item['price'] * $qty;
}


$prefill_name = '';
if (isset($_SESSION['user_id'])) {
    $login_user_id = intval($_SESSION['user_id']);

    $userConn = new mysqli('localhost','root','','gian_hang');
    if (!$userConn->connect_errno) {
        $stmt = $userConn->prepare("SELECT name FROM user_custom WHERE id=?");
        $stmt->bind_param('i', $login_user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $prefill_name = $row['name'];
        }
        $userConn->close();
    }
}

$ordered = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_name    = trim($_POST['name'] ?? '');
    $buyer_phone   = trim($_POST['phone'] ?? '');
    $buyer_address = trim($_POST['address'] ?? '');
    $login_user_id = intval($_SESSION['user_id'] ?? 0);


    if (empty($buyer_name) && !empty($prefill_name)) {
        $buyer_name = $prefill_name;
    }

    if (!isset($conn) || $conn->connect_error) {
        $error_message = "Lỗi kết nối Database Admin.";
    } elseif (empty($cart)) {
        $error_message = "Giỏ hàng trống.";
    } else {

        $conn->begin_transaction();
        try {

            $stmt = $conn->prepare("INSERT INTO customers (name, phone, address) VALUES (?,?,?)");
            $stmt->bind_param('sss', $buyer_name, $buyer_phone, $buyer_address);
            $stmt->execute();
            $customer_id = $conn->insert_id;
            $stmt->close();

            $status = 'pending';
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

            foreach ($cart_for_checkout as $product_id => $item) {
                $qty = intval($item['qty'] ?? 1);
                $name = $item['name'];
                $price = floatval($item['price']);
                $subtotal = $qty * $price;
                $pid = is_numeric($product_id) ? intval($product_id) : 0;
                $pid_exists = false;
                if ($pid > 0) {
                    $resCheck = $conn->query("SELECT id FROM products WHERE id = " . $pid . " LIMIT 1");
                    if ($resCheck && $resCheck->num_rows > 0) $pid_exists = true;
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
            if (!empty($selected_ids)) {
                foreach ($selected_ids as $sid) {
                    unset($_SESSION['cart'][$sid]);
                }
            } else {
                unset($_SESSION['cart']);
            }
            $ordered = true;

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<div class="checkout-container">
        <h2>Thanh Toán</h2>
        
        <?php if (!empty($error_message)): ?>
            <div style="color:red; text-align:center; margin-bottom:20px;">
                <?= htmlspecialchars($error_message) ?> <br>
                <a href="index.php?page=4">Quay về giỏ hàng</a>
            </div>
        <?php endif; ?>

        <?php if ($ordered): ?>
            <div style="color:green; text-align:center; padding:20px; background:#e8f5e9;">
                <h3>Đặt hàng thành công!</h3>
                <a href="index.php?page=4">Xem lịch sử đơn hàng</a>
            </div>
        <?php elseif (!empty($cart_for_checkout)): ?>
            <div class="checkout-layout">
                <form method="POST" class="checkout-form" id="checkoutForm" action="index.php?page=5">
                    <?php if (!empty($selected_ids)): foreach ($selected_ids as $sid): ?>
                        <input type="hidden" name="selected[]" value="<?= intval($sid) ?>">
                    <?php endforeach; endif; ?>

                    <div class="form-group">
                        <label>Họ tên:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($prefill_name) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại:</label>
                        <input type="tel" name="phone" required pattern="[0-9]{10,11}">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ:</label>
                        <input type="text" name="address" required>
                    </div>
                    <div style="margin-top:10px">
                        <label style="display:block;margin-bottom:8px">Phương thức thanh toán:</label>
                        <label style="display:flex;gap:8px;align-items:center"><input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)</label>
                        <label style="display:flex;gap:8px;align-items:center;margin-top:6px"><input type="radio" name="payment_method" value="vnpay"> Thanh toán qua VNPay</label>
                        <button type="submit" class="btn-pay" style="width:100%;margin-top:10px">Xác nhận và Thanh toán</button>
                    </div>
                </form>

                <aside class="summary-box">
                    <h4>Đơn hàng (<?= count($cart_for_checkout) ?>)</h4>
                    <?php foreach ($cart_for_checkout as $pid => $it): $qq = intval($it['qty'] ?? 1); $subt = $qq * floatval($it['price']); ?>
                        <div class="item"><div><?= htmlspecialchars($it['name']) ?> x <?= $qq ?></div><div><?= number_format($subt) ?> đ</div></div>
                    <?php endforeach; ?>
                    <div style="margin-top:10px;font-weight:700;display:flex;justify-content:space-between">Tổng:<span><?= number_format($total) ?> VND</span></div>
                    <?php if (!empty($selected_ids)): ?>
                        <div style="margin-top:8px;font-size:0.95rem;color:#555">Sau khi đặt, những sản phẩm chưa chọn vẫn giữ trong giỏ hàng.</div>
                    <?php endif; ?>
                </aside>
            </div>
        <?php elseif (!empty($cart)): ?>
            <div style="text-align:center;padding:20px;background:#fff;border-radius:8px">Bạn chưa chọn sản phẩm để thanh toán. <a href="index.php?page=4">Quay về giỏ hàng</a></div>
        <?php else: ?>
            <p style="text-align:center">Giỏ hàng trống</p>
        <?php endif; ?>
    </div>
<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(ev){
    const fm = this;
    const method = fm.querySelector('input[name="payment_method"]:checked')?.value;
    if (method === 'vnpay') {
        fm.action = 'vnpay_start.php';
    } else {
        fm.action = '';
    }
});
</script>
