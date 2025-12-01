<?php
if (!isset($_GET['id'])):
    echo "<script>alert('Không tìm thấy sản phẩm.'); window.location='index.php?page=1';</script>";
    return;
endif;
$product_id = intval($_GET['id']);
$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = $product_id";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    echo "<script>alert('Sản phẩm không tồn tại.'); window.location='index.php?page=1';</script>";
    return;
}
$product = $res->fetch_assoc();
?>

<main>
    <div class="product-detail-container">
        <div class="product-detail-image">
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://via.placeholder.com/500x400?text=No+Image';">
        </div>
        <div class="product-detail-info">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p>Danh mục: <a href="index.php?page=2&categoryID=<?= $product['category_id'] ?>" style="color:#a81515; text-decoration:none;"><?= htmlspecialchars($product['category_name']) ?></a></p>
            <p class="price"><?= number_format($product['price'],0,',','.') ?>₫</p>
            <p><strong>Mô tả chi tiết:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p>Tồn kho: <strong><?= intval($product['stock']) ?></strong> sản phẩm</p>
            <a href="add-to-cart.php?id=<?= $product_id ?>&redirect=<?= urlencode('index.php?page=3&id='.$product_id) ?>" class="btn-cart">Thêm vào giỏ hàng</a>
            <a href="javascript:history.back()" style="display:block; margin-top:20px; color:#555; text-decoration:none;">⬅Quay lại trang trước</a>
        </div>
    </div>
</main>

<section style="max-width:1100px;margin:18px auto;padding:0 18px">
    <div class="card" style="padding:16px">
        <h3>Đánh giá cho sản phẩm này</h3>
        <?php
        $tblCheck = $conn->query("SHOW TABLES LIKE 'feedbacks'");
        if (!$tblCheck || $tblCheck->num_rows == 0) {
        }
        $col = $conn->query("SHOW COLUMNS FROM feedbacks LIKE 'rating'");
        if (!$col || $col->num_rows == 0) {
            @$conn->query("ALTER TABLE feedbacks ADD COLUMN rating TINYINT DEFAULT NULL");
        }
        $col2 = $conn->query("SHOW COLUMNS FROM feedbacks LIKE 'product_id'");
        if (!$col2 || $col2->num_rows == 0) {
            @$conn->query("ALTER TABLE feedbacks ADD COLUMN product_id INT DEFAULT NULL");
        }

        $avgRes = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM feedbacks WHERE product_id = $product_id AND status='published'");
        $avg = 0; $total = 0;
        if ($avgRes && $row = $avgRes->fetch_assoc()) { $avg = round(floatval($row['avg_rating'] ?? 0),1); $total = intval($row['total']); }
        ?>
        <p style="margin:6px 0;color:#444">Điểm trung bình: <strong style="color:#c62828"><?= $avg ?: '—' ?></strong> (<?= $total ?> đánh giá)</p>

        <?php
        $r = $conn->query("SELECT customer_name, rating, message, created_at FROM feedbacks WHERE product_id = $product_id AND status='published' ORDER BY created_at DESC LIMIT 20");
        if ($r && $r->num_rows > 0) {
            echo '<div style="display:grid;gap:12px">';
            while($rev = $r->fetch_assoc()){
                ?>
                <div style="background:#fff;padding:12px;border-radius:8px">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <strong><?= htmlspecialchars($rev['customer_name']) ?></strong>
                        <span style="color:#c62828;font-weight:700"><?= intval($rev['rating']) ?>/5</span>
                    </div>
                    <p style="margin:6px 0;color:#333"><?= nl2br(htmlspecialchars($rev['message'])) ?></p>
                    <small style="color:#777"><?= htmlspecialchars($rev['created_at']) ?></small>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p class="text-muted">Chưa có đánh giá cho sản phẩm này. Hãy là người đầu tiên đánh giá!</p>';
        }
        ?>

        <div style="margin-top:14px;border-top:1px dashed #eee;padding-top:14px">
            <h4>Gửi đánh giá của bạn</h4>
            <?php
            $user_id = intval($_SESSION['user_id'] ?? 0);
            $can_review = true;
            $review_message = '';
            
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
                        $can_review = false;
                        $review_message = 'Bạn chỉ có thể đánh giá những sản phẩm đã mua.';
                    }
                    $purchaseCheck->close();
                }
            } else {
                $can_review = false;
                $review_message = 'Vui lòng <a href="/shop/login.php" style="color:#c62828;text-decoration:underline">đăng nhập</a> để đánh giá sản phẩm.';
            }
            
            if ($can_review): ?>
            <form action="/shop/feedback_submit.php" method="post">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <input type="text" name="name" placeholder="Tên của bạn" required style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px">
                    <select name="rating" required style="width:110px;padding:8px;border:1px solid #ddd;border-radius:6px">
                        <option value="">Đánh giá</option>
                        <option value="5">5 - Xuất sắc</option>
                        <option value="4">4 - Tốt</option>
                        <option value="3">3 - Trung bình</option>
                        <option value="2">2 - Kém</option>
                        <option value="1">1 - Rất tệ</option>
                    </select>
                </div>
                <div style="margin-bottom:8px"><input type="email" name="email" placeholder="Email (tùy chọn)" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px"></div>
                <div style="margin-bottom:8px"><textarea name="message" placeholder="Viết đánh giá của bạn..." required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;min-height:80px"></textarea></div>
                <div style="text-align:right"><button class="btn" type="submit">Gửi đánh giá</button></div>
            </form>
            <?php else: ?>
            <p style="color:#c62828;font-weight:600"><?= $review_message ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
