<?php
$cart = [];

$uid = intval($_SESSION['user_id'] ?? 0);
if ($uid > 0 && isset($conn)) {
    if (isset($_GET['action'], $_GET['id'])) {
        $pid = intval($_GET['id']);
        if ($_GET['action'] === 'increase') {
            $stmt = $conn->prepare("UPDATE carts SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param('ii', $uid, $pid);
            $stmt->execute();
            $stmt->close();
        }
        if ($_GET['action'] === 'decrease') {
            $stmt = $conn->prepare("UPDATE carts SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param('ii', $uid, $pid);
            $stmt->execute();
            $stmt->close();

            $del = $conn->prepare("DELETE FROM carts WHERE user_id = ? AND product_id = ? AND quantity <= 0");
            $del->bind_param('ii', $uid, $pid);
            $del->execute();
            $del->close();
        }
        echo "<script>location.href='index.php?page=4';</script>";
        exit;
    }

    if (isset($_GET['remove'])) {
        $pid = intval($_GET['remove']);
        $stmt = $conn->prepare("DELETE FROM carts WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param('ii', $uid, $pid);
        $stmt->execute();
        $stmt->close();
        echo "<script>location.href='index.php?page=4';</script>";
        exit;
    }

    $sql = "SELECT c.product_id, c.quantity, c.price_at_add, p.name, p.image FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $pid = intval($row['product_id']);
        $cart[$pid] = [
            'name'  => $row['name'],
            'price' => $row['price_at_add'],
            'image' => $row['image'],
            'qty'   => intval($row['quantity'])
        ];
    }
    $stmt->close();

} else {
    if (isset($_GET['action'], $_GET['id'])) {
        $id = intval($_GET['id']);
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) {
            if ($_GET['action'] === 'increase') $_SESSION['cart'][$id]['qty']++;
            if ($_GET['action'] === 'decrease') {
                $_SESSION['cart'][$id]['qty']--;
                if ($_SESSION['cart'][$id]['qty'] <= 0) unset($_SESSION['cart'][$id]);
            }
        }
        echo "<script>location.href='index.php?page=4';</script>";
        exit;
    }
    if (isset($_GET['remove'])) {
        $id = intval($_GET['remove']);
        if (isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]);
        echo "<script>location.href='index.php?page=4';</script>";
        exit;
    }

    $cart = $_SESSION['cart'] ?? [];
}

$history_orders = [];
if (isset($_SESSION['user_id']) && isset($conn)) {
    $uid = intval($_SESSION['user_id']);
    $sql = "SELECT * FROM orders WHERE id_user = ? ORDER BY order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($row = $res->fetch_assoc()) {
        $oid = $row['id'];
        $itemSql = "SELECT * FROM order_items WHERE order_id = ?";
        $stmtItem = $conn->prepare($itemSql);
        $stmtItem->bind_param('i', $oid);
        $stmtItem->execute();
        $resItem = $stmtItem->get_result();
        
        $products = [];
        while ($p = $resItem->fetch_assoc()) {
            $products[] = $p;
        }
        $row['products'] = $products;
        $history_orders[] = $row;
        $stmtItem->close();
    }
    $stmt->close();
}

?>

<div class="cart-container">
    <h2>Giỏ Hàng Của Bạn</h2>
    <?php if (!empty($cart)): ?>
        <form id="cartForm" method="get" action="index.php">
            <input type="hidden" name="page" value="5">
            <?php
            $total = 0;
            foreach ($cart as $ci) {
                $q = intval($ci['qty'] ?? 1);
                $total += floatval($ci['price']) * $q;
            }
            ?>
            <div style="margin-bottom:8px">
                <label><input type="checkbox" id="selectAll"> Chọn tất cả</label>
            </div>
            <table class="cart-table" width="100%" border="0" style="border-collapse:collapse; margin-bottom:12px;">
                <thead>
                <tr>
                    <th style="width:44px"></th>
                    <th style="text-align:left">Sản phẩm</th>
                    <th>Giá</th>
                    <th>SL</th>
                    <th>Tổng</th>
                    <th>Xóa</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cart as $id => $item): 
                    $qty = intval($item['qty'] ?? 1);
                    $sub = $item['price'] * $qty;
                ?>
                <tr data-id="<?= $id ?>" data-price="<?= htmlspecialchars($item['price']) ?>" data-qty="<?= $qty ?>">
                    <td><input type="checkbox" class="select-item" name="selected[]" value="<?= $id ?>"></td>
                    <td class="td-name">
                        <img src="uploads/<?= htmlspecialchars($item['image'] ?? '') ?>" alt="" style="width:56px;height:40px;object-fit:cover;border-radius:6px;margin-right:8px;vertical-align:middle"> 
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td class="td-price"><?= number_format($item['price']) ?></td>
                    <td class="td-qty">
                        <a href="index.php?page=4&action=decrease&id=<?= $id ?>">-</a>
                        <span class="qty-value"><?= $qty ?></span>
                        <a href="index.php?page=4&action=increase&id=<?= $id ?>">+</a>
                    </td>
                    <td class="td-sub"><?= number_format($sub) ?></td>
                    <td><a href="index.php?page=4&remove=<?= $id ?>">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td colspan="3" style="text-align:right"><strong>Tổng giỏ (tất cả):</strong></td>
                    <td colspan="2"><strong><?= number_format($total) ?> VND</strong></td>
                </tr>
                </tfoot>
            </table>

            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                <div id="selectedSummary">Đã chọn: <strong id="selectedCount">0</strong> sản phẩm — Tổng: <strong id="selectedTotal">0</strong> VND</div>
                <button id="checkoutSelected" type="submit" style="background:#1976d2;color:#fff;padding:8px 14px;border:none;border-radius:6px" disabled>Thanh Toán Đã Chọn</button>
                <a href="index.php?page=5" style="background:green; color:white; padding:10px 14px; text-decoration:none;border-radius:6px">Thanh Toán Toàn bộ</a>
            </div>
        </form>

        <script>
        (function(){
            function fmt(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
            const selectAll = document.getElementById('selectAll');
            const checkboxes = Array.from(document.querySelectorAll('.select-item'));
            const selectedCount = document.getElementById('selectedCount');
            const selectedTotal = document.getElementById('selectedTotal');
            const checkoutBtn = document.getElementById('checkoutSelected');

            if (!selectedCount || !selectedTotal || !checkoutBtn) return; 

            function recompute(){
                let count = 0; let total = 0;
                checkboxes.forEach(ch =>{
                    if (!ch) return;
                    if (ch.checked){
                        count++;
                        const tr = ch.closest('tr');
                        const price = parseFloat(tr.getAttribute('data-price') || '0');
                        const qty = parseInt(tr.getAttribute('data-qty') || '0');
                        total += price * qty;
                    }
                });
                selectedCount.textContent = count;
                selectedTotal.textContent = fmt(Math.round(total));
                checkoutBtn.disabled = count === 0;
            }

            if (selectAll) selectAll.addEventListener('change', function(){
                checkboxes.forEach(ch=>{ if (ch) ch.checked = this.checked; }); recompute();
            });
            checkboxes.forEach(ch=> ch.addEventListener('change', recompute));
            recompute();
        })();
        </script>
    <?php else: ?>
        <p>Giỏ hàng đang trống.</p>
    <?php endif; ?>


    <hr style="margin: 30px 0;">
    <h3>Lịch sử mua hàng</h3>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <p>Vui lòng <a href="../login.php">đăng nhập</a> để xem lịch sử đơn hàng.</p>
    <?php elseif (empty($history_orders)): ?>
        <p>Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <?php foreach ($history_orders as $ord): ?>
            <form class="order-form" method="post" action="index.php?page=4">
                <div class="order-summary">
                    <div class="left">
                        <strong>Đơn hàng #<?= $ord['id'] ?></strong>
                        <div class="meta"><?= htmlspecialchars($ord['order_date']) ?> • Người nhận: <?= htmlspecialchars($ord['customer_name']) ?></div>
                    </div>
                    <div style="text-align:right">
                        <div style="margin-bottom:6px"><strong><?= number_format($ord['total_amount']) ?> VND</strong></div>
                        <div class="order-actions">
                            <span class="status"><?= ucfirst($ord['status']) ?></span>
                            <button type="button" class="toggle-order" data-target="order-<?= $ord['id'] ?>">Xem</button>
                        </div>
                    </div>
                </div>

                <div id="order-<?= $ord['id'] ?>" class="order-details" style="display:none">
                    <strong>Chi tiết sản phẩm:</strong>
                    <ul>
                    <?php foreach ($ord['products'] as $prod): ?>
                        <li><?= htmlspecialchars($prod['product_name']) ?> x <?= $prod['quantity'] ?> = <?= number_format($prod['subtotal']) ?> đ</li>
                    <?php endforeach; ?>
                    </ul>
                    <div style="margin-top:8px;color:#666;font-size:0.95rem">Trạng thái: <?= htmlspecialchars($ord['status']) ?></div>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="center-btn">
        <a href="index.php" class="btn-back-home">← Tiếp tục mua sắm</a>
    </div>
</div>
<script>
(function(){
    document.querySelectorAll('.toggle-order').forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.getAttribute('data-target');
            const el = document.getElementById(id);
            if (!el) return;
            el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
        });
    });
})();
</script>
