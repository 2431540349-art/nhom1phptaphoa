<?php
$categoryID = isset($_GET['categoryID']) ? intval($_GET['categoryID']) : 0;
$catName = "Tất cả Sản phẩm";
$sqlFilter = "";
if ($categoryID > 0) {
    $q = $conn->query("SELECT name FROM categories WHERE id = $categoryID");
    if ($q && $q->num_rows > 0) {
        $catName = $q->fetch_assoc()['name'];
        $sqlFilter = "WHERE category_id = $categoryID";
    } else {
        $categoryID = 0;
    }
}
$perPage = 30;
$pageNum = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($pageNum - 1) * $perPage;

$qSearch = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($qSearch !== '') {
    $safeQ = $conn->real_escape_string($qSearch);
    $sqlFilter = ($sqlFilter === '' ? "WHERE name LIKE '%{$safeQ}%'" : $sqlFilter . " AND name LIKE '%{$safeQ}%'");
}

$countSql = "SELECT COUNT(*) AS total FROM products " . $sqlFilter;
$countRes = $conn->query($countSql);
$total = 0;
if ($countRes && $row = $countRes->fetch_assoc()) {
    $total = intval($row['total']);
}
$totalPages = $total > 0 ? ceil($total / $perPage) : 1;

$sql = "SELECT * FROM products " . $sqlFilter . " ORDER BY id DESC LIMIT " . intval($offset) . "," . intval($perPage);
$result = $conn->query($sql);
?>

<main>
    <h2 style="text-align:center;"><?= htmlspecialchars($catName) ?></h2>

    <div class="products">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($p = $result->fetch_assoc()): ?>
            <div class="card product-card" data-id="<?= $p['id'] ?>">
                <a href="index.php?page=3&id=<?= $p['id'] ?>">
                    <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.src='https://via.placeholder.com/220x160?text=No+Image';">
                </a>
                <h3>
                    <a href="index.php?page=3&id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                </h3>
                <p><?= number_format($p['price'],0,',','.') ?>₫</p>
                <div style="display:flex;gap:8px;align-items:center">
                    <a href="index.php?page=2&categoryID=<?= $categoryID ?>&add_to_cart=<?= $p['id'] ?>" class="btn-cart">Thêm vào giỏ</a>
                    <button class="btn-fav" data-id="<?= $p['id'] ?>" aria-label="Yêu thích">Yêu thích</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; width:100%; margin-top: 20px;">Chưa có sản phẩm nào trong danh mục này.</p>
    <?php endif; ?>
    </div>
    
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <?php
            $baseUrl = 'index.php?page=2';
            if ($categoryID > 0) $baseUrl .= '&categoryID=' . intval($categoryID);
            if (isset($_GET['q']) && $_GET['q'] !== '') $baseUrl .= '&q=' . urlencode($_GET['q']);
        ?>
        <nav class="pagination" aria-label="Trang sản phẩm">
            <?php if ($pageNum > 1): ?>
                <a href="<?= $baseUrl . '&p=' . ($pageNum - 1) ?>" class="page-prev">‹ Trước</a>
            <?php else: ?>
                <span class="page-prev disabled">‹ Trước</span>
            <?php endif; ?>

            <?php
            $start = max(1, $pageNum - 3);
            $end = min($totalPages, $pageNum + 3);
            if ($start > 1) echo '<a class="page-num" href="' . $baseUrl . '&p=1">1</a>' . ($start > 2 ? '<span class="dots">…</span>' : '');
            for ($pi = $start; $pi <= $end; $pi++):
                if ($pi == $pageNum):
                    echo '<span class="page-num current">' . $pi . '</span>';
                else:
                    echo '<a class="page-num" href="' . $baseUrl . '&p=' . $pi . '">' . $pi . '</a>';
                endif;
            endfor;
            if ($end < $totalPages) echo ($end < $totalPages - 1 ? '<span class="dots">…</span>' : '') . '<a class="page-num" href="' . $baseUrl . '&p=' . $totalPages . '">' . $totalPages . '</a>';
            ?>

            <?php if ($pageNum < $totalPages): ?>
                <a href="<?= $baseUrl . '&p=' . ($pageNum + 1) ?>" class="page-next">Sau ›</a>
            <?php else: ?>
                <span class="page-next disabled">Sau ›</span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <a href="index.php?page=1" style="display:block; text-align:center; margin-top: 20px; text-decoration: none; color: #a81515; font-weight: bold;">Quay lại Trang chủ</a>
</main>
