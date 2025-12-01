<?php
if (!isset($categories) || !is_array($categories)) {
    $categories = function_exists('getCategories') ? getCategories() : [];
}
if (!isset($products) || !is_array($products)) {
    $products = function_exists('getProducts') ? getProducts() : [];
}
?>

<div class="banner-slider">
    <div class="viewport">
        <div class="slides">
            <div class="slide"><img src="uploads/banner/1.jpg" alt="Banner 1"></div>
            <div class="slide"><img src="uploads/banner/2.jpg" alt="Banner 2"></div>
            <div class="slide"><img src="uploads/banner/3.jpg" alt="Banner 3"></div>
            <div class="slide"><img src="uploads/banner/4.jpg" alt="Banner 4"></div>
        </div>
    </div>
</div>

<script>
(function(){
    try{
        const viewport = document.querySelector('.banner-slider .viewport');
        const track = document.querySelector('.banner-slider .slides');
        const slides = document.querySelectorAll('.banner-slider .slide');
        if (!track || slides.length === 0) return;
        let idx = 0, count = slides.length, interval = 4000;

        function go(to){
            idx = (to + count) % count;
            const offset = -idx * 100;
            track.style.transform = 'translateX(' + offset + '%)';
        }
        let timer = setInterval(()=> go(idx+1), interval);
        if (viewport){
            viewport.addEventListener('mouseenter', ()=> clearInterval(timer));
            viewport.addEventListener('mouseleave', ()=> { timer = setInterval(()=> go(idx+1), interval); });
        }

        go(0);
        window.addEventListener('resize', ()=> go(idx));
    }catch(e){ console && console.warn && console.warn(e); }
})();
</script>

<main>
    <h2 style="text-align:center;">Danh mục loại Sản Phẩm</h2>
    <div class="categories">
    <?php
        if (is_array($categories)):
            foreach ($categories as $c):
    ?>
        <a href="index.php?page=2&categoryID=<?= $c['id'] ?>" class="card">
            <div style="font-size:28px;line-height:1;"><?= $c['icon'] ?></div>
            <h3><?= htmlspecialchars($c['name']) ?></h3>
            <p><?= intval($c['count']) ?> sản phẩm</p>
        </a>
    <?php
            endforeach;
        endif;
    ?>
    </div>

    <h2 style="text-align:center;">Các Sản Phẩm Nổi Bật</h2>
    <?php
            $featured = function_exists('getRecentProducts') ? getRecentProducts(6) : array_slice($products, 0, 6);
    ?>
    <div class="products">
    <?php foreach ($featured as $p): ?>
        <div class="card product-card" data-id="<?= $p['id'] ?>">
            <a href="index.php?page=3&id=<?= $p['id'] ?>">
                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            </a>
            <h3>
                <a href="index.php?page=3&id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                    <?= htmlspecialchars($p['name']) ?>
                </a>
            </h3>
            <p><?= number_format($p['price'],0,',','.') ?>₫</p>
            <div style="display:flex;gap:8px;align-items:center">
                <a href="index.php?add_to_cart=<?= $p['id'] ?>&page=1" class="btn-cart">Thêm vào giỏ</a>
                <button class="btn-fav" data-id="<?= $p['id'] ?>" aria-label="Yêu thích">Yêu thích</button>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <h2 style="text-align:center;margin-top:28px;">Tất cả sản phẩm</h2>
    <?php
        $ap = isset($_GET['ap']) ? max(1, intval($_GET['ap'])) : 1;
        $perPageAll = 30;
        $offsetAll = ($ap - 1) * $perPageAll;
        $qHome = isset($_GET['q']) ? trim($_GET['q']) : '';
        $totalAll = function_exists('getProductsCount') ? getProductsCount($qHome, 0) : count($products);
        $totalPagesAll = $totalAll > 0 ? ceil($totalAll / $perPageAll) : 1;
        $allProducts = function_exists('getProductsPaginated') ? getProductsPaginated($perPageAll, $offsetAll, $qHome, 0) : array_slice($products, $offsetAll, $perPageAll);
    ?>
    <div class="products">
    <?php foreach ($allProducts as $p): ?>
        <div class="card product-card" data-id="<?= $p['id'] ?>">
            <a href="index.php?page=3&id=<?= $p['id'] ?>">
                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            </a>
            <h3>
                <a href="index.php?page=3&id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
                    <?= htmlspecialchars($p['name']) ?>
                </a>
            </h3>
            <p><?= number_format($p['price'],0,',','.') ?>₫</p>
            <div style="display:flex;gap:8px;align-items:center">
                <a href="index.php?add_to_cart=<?= $p['id'] ?>&page=1" class="btn-cart">Thêm vào giỏ</a>
                <button class="btn-fav" data-id="<?= $p['id'] ?>" aria-label="Yêu thích">Yêu thích</button>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <?php if ($totalPagesAll > 1): ?>
        <?php
            $base = 'index.php?page=1';
            if ($qHome !== '') $base .= '&q=' . urlencode($qHome);
        ?>
        <nav class="pagination" aria-label="Trang tất cả sản phẩm">
            <?php if ($ap > 1): ?>
                <a href="<?= $base . '&ap=' . ($ap - 1) ?>" class="page-prev">‹ Trước</a>
            <?php else: ?>
                <span class="page-prev disabled">‹ Trước</span>
            <?php endif; ?>

            <?php
            $startA = max(1, $ap - 3);
            $endA = min($totalPagesAll, $ap + 3);
            if ($startA > 1) echo '<a class="page-num" href="' . $base . '&ap=1">1</a>' . ($startA > 2 ? '<span class="dots">…</span>' : '');
            for ($ai = $startA; $ai <= $endA; $ai++):
                if ($ai == $ap):
                    echo '<span class="page-num current">' . $ai . '</span>';
                else:
                    echo '<a class="page-num" href="' . $base . '&ap=' . $ai . '">' . $ai . '</a>';
                endif;
            endfor;
            if ($endA < $totalPagesAll) echo ($endA < $totalPagesAll - 1 ? '<span class="dots">…</span>' : '') . '<a class="page-num" href="' . $base . '&ap=' . $totalPagesAll . '">' . $totalPagesAll . '</a>';
            ?>

            <?php if ($ap < $totalPagesAll): ?>
                <a href="<?= $base . '&ap=' . ($ap + 1) ?>" class="page-next">Sau ›</a>
            <?php else: ?>
                <span class="page-next disabled">Sau ›</span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</main>
</main>
