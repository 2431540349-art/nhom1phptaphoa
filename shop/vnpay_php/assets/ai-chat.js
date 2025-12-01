(function(){
  function el(html){ var d=document.createElement('div'); d.innerHTML=html.trim(); return d.firstChild; }

  var btn = el('<div class="ai-chat-button" id="aiChatBtn">💬</div>');
  document.body.appendChild(btn);

  var panel = el('<div class="ai-chat-panel ai-hidden" id="aiChatPanel">' +
    '<div class="ai-chat-header">Trợ lý mua hàng (AI)</div>' +
    '<div class="ai-chat-body" id="aiChatBody"></div>' +
    '<div class="ai-chat-footer"><input class="ai-chat-input" id="aiChatInput" placeholder="Bạn cần tư vấn gì?">' +
    '<button id="aiSendBtn" class="btn btn-primary">Gửi</button></div>' +
    '</div>');
  document.body.appendChild(panel);

  var aiChatBtn = document.getElementById('aiChatBtn');
  var aiChatPanel = document.getElementById('aiChatPanel');
  var aiChatBody = document.getElementById('aiChatBody');
  var aiChatInput = document.getElementById('aiChatInput');
  var aiSendBtn = document.getElementById('aiSendBtn');

  function openPanel(){ aiChatPanel.classList.remove('ai-hidden'); aiChatInput.focus(); }
  function closePanel(){ aiChatPanel.classList.add('ai-hidden'); }
  aiChatBtn.addEventListener('click', function(){ if(aiChatPanel.classList.contains('ai-hidden')) openPanel(); else closePanel(); });

  function appendMessage(text, who){
    var wrap = document.createElement('div'); wrap.className = 'ai-msg '+who;
    var bubble = document.createElement('div'); bubble.className = 'bubble'; bubble.textContent = text;
    wrap.appendChild(bubble);
    aiChatBody.appendChild(wrap);
    aiChatBody.scrollTop = aiChatBody.scrollHeight;
  }

  function setLoading(on){
    if(on){ var ld = document.createElement('div'); ld.className='ai-loading'; ld.id='aiLoading'; ld.textContent='Đang trả lời...'; aiChatBody.appendChild(ld); aiChatBody.scrollTop = aiChatBody.scrollHeight; }
    else{ var el = document.getElementById('aiLoading'); if(el) el.remove(); }
  }

  function sendMessage(){
    var text = aiChatInput.value.trim(); if(!text) return;
    appendMessage(text,'user'); aiChatInput.value=''; setLoading(true); aiSendBtn.disabled=true;

    fetch('/shop/ai_chat.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ message: text }) })
    .then(function(r){ return r.json(); })
    .then(function(json){ setLoading(false); aiSendBtn.disabled=false; if(json && json.ok){
        appendMessage(json.reply || '[Không có phản hồi]','bot');
      } else {
        console.error('AI error response', json);
        if (json && json.fallback) {
          appendMessage(json.fallback, 'bot');
        } else {
          var m = json && (json.message || json.error || 'Không thể kết nối AI');
          var detail = json && json.raw ? ('\n' + String(json.raw).substring(0,800)) : '';
          appendMessage('Lỗi: '+m + detail,'bot');
        }
      } })
    .catch(function(err){ setLoading(false); aiSendBtn.disabled=false; appendMessage('Lỗi kết nối AI','bot'); console.error(err); });
  }

  aiSendBtn.addEventListener('click', sendMessage);
  aiChatInput.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); sendMessage(); } });

})();
