<?php
session_start();
require_once __DIR__ . '/partials/helpers.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Bách Hóa Đa Dạng</title>
    <link rel="stylesheet" href="public.css">
    <link rel="stylesheet" href="/shop/vnpay_php/assets/ai-chat.css">
</head>
<body>

<header>
    <div class="header-inner">
        <div class="header-left">
            <button id="toggleSidebarBtn" class="sidebar-toggle" aria-label="Mở danh mục">☰</button>
            <h1>TẠP HÓA 5.0</h1>
            <form class="header-search" action="index.php" method="get">
                <input type="hidden" name="page" value="2">
                <input type="text" name="q" placeholder="Tìm sản phẩm..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button type="submit">Tìm</button>
            </form>
        </div>
        <div class="header-center" role="navigation" aria-label="Main navigation">
            <a href="index.php?page=1" class="action-link">Trang chủ</a>
            <a href="index.php?page=6" class="action-link">Yêu thích</a>
            <a href="index.php?page=4" class="action-link">Giỏ hàng (<?= function_exists('getCartCount') ? getCartCount() : (isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0) ?>)</a>
            <a href="index.php?page=7" class="action-link">Trò chuyện</a>
        </div>
        <div class="header-right">
            <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=8" class="action-link action-user"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                <a href="logout.php" class="action-link">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php" class="action-link">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<aside class="sidebar-menu-fixed">
    <h3 style="padding:0 12px"><strong>Danh Mục Sản Phẩm</strong></h3>
    <ul>
        <?php $categories = getCategories(); foreach ($categories as $c): ?>
            <li><a href="index.php?page=2&categoryID=<?= $c['id'] ?>" class="menu-item"><span class="cat-name"><?= htmlspecialchars($c['name']) ?></span><span class="count"><?= intval($c['count']) ?></span></a></li>
        <?php endforeach; ?>
    </ul>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

<main>
    <?php
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $products = getProducts();
    switch ($page) {
        case 2:
            include __DIR__ . '/partials/product-list.php';
            break;
        case 3:
            include __DIR__ . '/partials/product-detail.php';
            break;
        case 4:
            include __DIR__ . '/partials/cart.php';
            break;
        case 8:
            include __DIR__ . '/partials/profile.php';
            break;
        case 7:
            include __DIR__ . '/partials/messenger.php';
            break;
        case 6:
            include __DIR__ . '/partials/favorites.php';
            break;
        case 5:
            include __DIR__ . '/partials/checkout.php';
            break;
        case 1:
        default:
            include __DIR__ . '/partials/home.php';
            break;
    }
    ?>
</main>

<footer id="contact">
    <div class="footer-section">
        <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.43123456789!2d106.700000!3d10.780000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175290000000001%3A0x123456789abcdef!2sHo%20Chi%20Minh%2C%20Vietnam!5e0!3m2!1sen!2s!4v1694000000000" 
      width="100%" 
      height="200" 
      style="border:0; border-radius:12px;" 
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
    </div>
    <div class="footer-bottom"><p>© 2025 Bách Hóa Đa Dạng. All rights reserved.</p></div>
</footer>
<script src="/shop/vnpay_php/assets/ai-chat.js"></script>
<script>
(function(){
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    if (toggleBtn) toggleBtn.addEventListener('click', (e)=>{ e.preventDefault(); const open=document.body.classList.toggle('sidebar-open'); try{localStorage.setItem('sidebarOpen', open? '1':'0')}catch(e){} if(open) sidebarBackdrop.setAttribute('aria-hidden','false'); else sidebarBackdrop.setAttribute('aria-hidden','true'); });
    if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', ()=>{ document.body.classList.remove('sidebar-open'); sidebarBackdrop.setAttribute('aria-hidden','true'); try{localStorage.setItem('sidebarOpen','0')}catch(e){} });

    const openBtn = document.getElementById('openChatPublic');
    const popup = document.getElementById('chatPopup');
    const closeBtn = document.getElementById('closeChat');
    const form = document.getElementById('chatForm');
    function open(){ popup.classList.add('open'); popup.setAttribute('aria-hidden','false'); }
    function close(){ popup.classList.remove('open'); popup.setAttribute('aria-hidden','true'); }
    if (openBtn) openBtn.addEventListener('click', (e)=>{ e.preventDefault(); open(); });
    if (closeBtn) closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); close(); });
    form && form.addEventListener('submit', function(ev){ ev.preventDefault(); const data=new FormData(form); fetch('/chat_submit.php', { method:'POST', body:data, headers:{'X-Requested-With':'XMLHttpRequest'} }).then(r=>r.json().catch(()=>({ok:false,message:'Lỗi mạng'}))).then(json=>{ const body=document.getElementById('chatBody'); if(json.ok){ body.innerHTML='<p class="muted">'+(json.message||'Đã gửi.')+'</p>'; form.reset(); setTimeout(()=>close(),1400);} else { body.innerHTML='<p class="muted" style="color:#b71c1c">'+(json.message||'Lỗi')+'</p>'; } }).catch(()=>{ document.getElementById('chatBody').innerHTML='<p class="muted" style="color:#b71c1c">Lỗi kết nối</p>'; }); });
})();
</script>

<script>
(function(){
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

    function loadFavsLocal(){ try{ return JSON.parse(localStorage.getItem('shop_favs') || '{}'); }catch(e){ return {}; } }
    function saveFavsLocal(obj){ try{ localStorage.setItem('shop_favs', JSON.stringify(obj)); }catch(e){} }

    function setFavButton(btn, state){ if(!btn) return; btn.classList.toggle('active', !!state); btn.textContent = 'Yêu thích'; }

    const favButtons = Array.from(document.querySelectorAll('.btn-fav'));

    if (isLoggedIn) {
        fetch('/shop/api/get_favorites.php').then(r=>r.json()).then(json=>{
            const ids = (json && json.ok && Array.isArray(json.favorites)) ? json.favorites : [];
            const favSet = new Set(ids.map(x=>String(x)));
            favButtons.forEach(btn=>{
                const id = btn.getAttribute('data-id');
                setFavButton(btn, favSet.has(id));
                btn.addEventListener('click', function(ev){ ev.preventDefault(); const pid = this.getAttribute('data-id'); if(!pid) return; // POST toggle
                    fetch('/shop/api/toggle_favorite.php', { method: 'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ product_id: pid }) }).then(r=>r.json()).then(resp=>{
                        if (resp && resp.ok) setFavButton(this, !!resp.favorited);
                        else {
                            console && console.error && console.error('Toggle favorite failed', resp);
                        }
                    }).catch(err=>{ console && console.error && console.error(err); });
                });
            });
        }).catch(err=>{ console && console.error && console.error(err); });
    } else {
        const favs = loadFavsLocal();
        favButtons.forEach(btn=>{
            const id = btn.getAttribute('data-id');
            setFavButton(btn, !!favs[id]);
            btn.addEventListener('click', function(ev){ ev.preventDefault(); const pid = this.getAttribute('data-id'); if(!pid) return; const newState = !favs[pid]; favs[pid] = newState; if(!newState) delete favs[pid]; saveFavsLocal(favs); setFavButton(this, newState); });
        });
    }
})();
</script>

</body>
</html>