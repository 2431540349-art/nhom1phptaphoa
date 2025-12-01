<?php
?>
<div class="favorites-container" style="max-width:1100px;margin:18px auto;padding:12px">
    <h2>Sản phẩm yêu thích</h2>
    <p class="muted" id="favEmptyMsg" style="display:none">Bạn chưa đánh dấu sản phẩm nào là yêu thích. Hãy bấm "Yêu thích" trên các sản phẩm để thêm.</p>
    <style>
        #favGrid.favorites-loading{min-height:360px;display:flex;align-items:center;justify-content:center;color:var(--muted)}
        #favGrid .product-card{min-height:220px}
    </style>
    <div id="favGrid" class="products" aria-live="polite"></div>
</div>

<script>
(function(){
    function fmt(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
    const grid = document.getElementById('favGrid');
    const empty = document.getElementById('favEmptyMsg');
    if (!grid) return;

    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

    function renderProducts(ids, favMap){
        if (!ids || ids.length === 0) { empty.style.display = 'block'; return; }
        grid.classList.add('favorites-loading');
        grid.innerHTML = '<div>Đang tải sản phẩm yêu thích…</div>';
        fetch('/shop/api/get_products.php?ids=' + ids.join(','))
        .then(r=>r.json())
        .then(products=>{
            if (!products || !products.length) { empty.style.display='block'; return; }
            empty.style.display='none';
            grid.classList.remove('favorites-loading');
            grid.innerHTML = '';
            products.forEach(p=>{
                const card = document.createElement('div');
                card.className = 'card product-card';
                card.innerHTML = `
                    <a href="/shop/index.php?page=3&id=${p.id}" title="Xem chi tiết">
                      <img src="/shop/uploads/${p.image}" alt="${p.name}" style="width:100%;height:120px;object-fit:cover;border-radius:8px">
                    </a>
                    <h3 style="margin:8px 0 6px"><a href="/shop/index.php?page=3&id=${p.id}" style="color:inherit;text-decoration:none">${p.name}</a></h3>
                    <p style="margin:0 0 8px;color:#666">${fmt(Math.round(p.price))} VND</p>
                    <div style="display:flex;gap:8px;align-items:center">
                        <a class="btn-cart" href="/shop/add-to-cart.php?id=${p.id}&redirect=/shop/index.php?page=6">🛒 Thêm vào giỏ</a>
                        <button class="btn-fav" data-id="${p.id}" style="margin-left:auto">Yêu thích</button>
                        <a class="action-link" href="/shop/index.php?page=3&id=${p.id}">Xem chi tiết</a>
                    </div>
                `;
                grid.appendChild(card);
            });
        }).catch(err=>{ console.error(err); empty.style.display='block'; });
    }

    if (isLoggedIn) {
        fetch('/shop/api/get_favorites.php')
        .then(r=>r.json())
        .then(json=>{
            if (!json || !json.ok) { empty.style.display='block'; return; }
            const ids = json.favorites || [];
            renderProducts(ids);
        }).catch(err=>{ console.error(err); empty.style.display='block'; });
    } else {
        function loadFavs(){ try{ return JSON.parse(localStorage.getItem('shop_favs') || '{}'); }catch(e){ return {}; } }
        const favs = loadFavs();
        const ids = Object.keys(favs).filter(k=>favs[k]).map(k=>parseInt(k,10)).filter(Boolean);
        renderProducts(ids, favs);
    }
})();
</script>
