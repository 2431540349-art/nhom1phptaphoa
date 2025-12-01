<?php
?>
<div class="chat-page" style="max-width:900px;margin:18px auto;padding:12px">
    <h2>Liên hệ Admin</h2>
    <div style="display:grid;grid-template-columns:1fr 320px;gap:14px">
        <div>
            <div id="chatLog" style="height:420px;overflow:auto;background:#fff;border-radius:8px;padding:12px;border:1px solid #eee"></div>
            <form id="chatForm" style="margin-top:12px;background:#fff;padding:12px;border-radius:8px;border:1px solid #eee">
                <input type="hidden" name="product_id" value="">
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <input type="text" name="name" id="chatName" placeholder="Tên của bạn" required style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px">
                    <input type="text" name="phone" id="chatPhone" placeholder="Số điện thoại" style="width:140px;padding:8px;border:1px solid #ddd;border-radius:6px">
                </div>
                <div style="margin-bottom:8px"><textarea name="message" id="chatMessage" placeholder="Viết tin nhắn..." required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;min-height:90px"></textarea></div>
                <div style="display:flex;gap:8px;align-items:center">
                    <button type="submit" class="btn">Gửi</button>
                    <button type="button" id="clearChat" class="btn" style="background:#eee;color:#333">Xóa lịch sử cục bộ</button>
                    <div style="margin-left:auto;color:#666;font-size:0.9rem">Bạn có thể chat với Admin — họ sẽ phản hồi tại đây.</div>
                </div>
            </form>
        </div>
        <aside style="background:#fff;padding:12px;border-radius:8px;border:1px solid #eee">
            <h4>Hướng dẫn</h4>
            <p class="muted">Nhập tên và số điện thoại (tùy chọn) để admin có thể liên hệ lại. Tin nhắn sẽ được lưu trong hệ thống.</p>
            <hr>
            <h4>Hoạt động</h4>
            <div id="chatStatus" class="muted">Chưa có cuộc hội thoại.</div>
        </aside>
    </div>
</div>

<script>
(function(){
    const log = document.getElementById('chatLog');
    const form = document.getElementById('chatForm');
    const nameInput = document.getElementById('chatName');
    const phoneInput = document.getElementById('chatPhone');
    const msgInput = document.getElementById('chatMessage');
    const status = document.getElementById('chatStatus');
    const clearBtn = document.getElementById('clearChat');

    function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    try{ const meta = JSON.parse(localStorage.getItem('chat_meta')||'{}'); if(meta.name) nameInput.value = meta.name; if(meta.phone) phoneInput.value = meta.phone; }catch(e){}

    function renderMessages(items){
        log.innerHTML = '';
        if(!items || !items.length){ log.innerHTML = '<p class="muted">Chưa có tin nhắn.</p>'; status.textContent='Chưa có cuộc hội thoại.'; return; }
        items.forEach(it=>{
            const who = it.sender === 'admin' ? 'Admin' : esc(it.customer_name || 'Bạn');
            const cls = it.sender === 'admin' ? 'admin-msg' : 'user-msg';
            const el = document.createElement('div');
            el.style.marginBottom = '10px';
            el.innerHTML = '<div style="font-size:13px;color:#666">' + who + ' <small>(' + esc(it.created_at) + ')</small></div>' +
                           '<div style="background:'+(it.sender==='admin'?'#f1f8ff':'#e8f5e9')+';padding:8px;border-radius:8px;margin-top:6px;color:#111">' + esc(it.message) + '</div>';
            log.appendChild(el);
        });
        log.scrollTop = log.scrollHeight;
        status.textContent = 'Đã tải ' + items.length + ' tin nhắn.';
    }

    let lastFetch = 0;
    function fetchMessages(){
        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        if(!name && !phone){ renderMessages([]); return; }
        const url = '/shop/api/get_customer_messages.php?name=' + encodeURIComponent(name) + '&phone=' + encodeURIComponent(phone);
        fetch(url).then(r=>r.json()).then(json=>{ renderMessages(json); lastFetch = Date.now(); }).catch(()=>{ status.textContent='Lỗi tải tin nhắn'; });
    }

    let poll = setInterval(function(){ if(Date.now() - lastFetch > 4000) fetchMessages(); }, 5000);

    form.addEventListener('submit', function(ev){ ev.preventDefault(); const data = new FormData(form); fetch('/shop/chat_submit.php', { method:'POST', body:data, headers:{'X-Requested-With':'XMLHttpRequest'} }).then(r=>r.json()).then(json=>{ if(json.ok){
                try{ localStorage.setItem('chat_meta', JSON.stringify({name: nameInput.value.trim(), phone: phoneInput.value.trim()})); }catch(e){}
                msgInput.value = '';
                fetchMessages();
            } else { alert(json.message || 'Lỗi gửi'); }
        }).catch(()=>{ alert('Lỗi mạng'); }); });

    clearBtn.addEventListener('click', function(){ if(!confirm('Xóa lịch sử chat trên trình duyệt? (chỉ xóa local cache)')) return; try{ localStorage.removeItem('chat_meta'); }catch(e){} nameInput.value=''; phoneInput.value=''; renderMessages([]); status.textContent='Đã xóa dữ liệu cục bộ.'; });

    nameInput.addEventListener('change', fetchMessages);
    phoneInput.addEventListener('change', fetchMessages);

    fetchMessages();
})();
</script>
