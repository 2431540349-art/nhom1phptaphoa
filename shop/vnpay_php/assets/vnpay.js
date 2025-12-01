document.addEventListener('DOMContentLoaded', function() {
  var body = document.body || document.getElementsByTagName('body')[0];

  if (body.classList && body.classList.contains('vnpay-page')) {
    var form = document.getElementById('frmCreateOrder');
    if (form) {
      var submit = form.querySelector('button[type=submit]');
      form.addEventListener('submit', function (e) {
        var amountEl = form.querySelector('input[name="amount"], #amount');
        var amt = amountEl ? parseFloat(amountEl.value) : NaN;
        if (isNaN(amt) || amt <= 0) {
          e.preventDefault();
          try { amountEl.focus(); } catch (ex) {}
          alert('Vui lòng nhập số tiền hợp lệ.');
          return;
        }
        if (submit && submit.disabled) { e.preventDefault(); return; }
        if (submit) {
          submit.disabled = true;
          submit.dataset.orig = submit.innerHTML;
          submit.innerHTML = 'Đang chuyển...';
        }
      }, {passive:false});
    }

    try {
      var radios = Array.prototype.slice.call(document.querySelectorAll('input[name="bankCode"]'));
      radios.forEach(function (r) {
        var lbl = r.nextElementSibling;
        if (lbl && lbl.tagName && lbl.tagName.toLowerCase() === 'label') {
          lbl.style.cursor = 'pointer';
          lbl.addEventListener('click', function () {
            radios.forEach(function (rr) {
              var l2 = rr.nextElementSibling;
              if (l2 && l2.tagName && l2.tagName.toLowerCase() === 'label') {
                l2.classList.remove('vnp-selected');
                l2.style.boxShadow = '';
                l2.style.border = '';
              }
            });
            r.checked = true;
            lbl.classList.add('vnp-selected');
            lbl.style.boxShadow = '0 8px 24px rgba(11,118,209,0.06)';
            lbl.style.border = '1px solid rgba(11,118,209,0.12)';
          });
          if (r.checked) {
            lbl.classList.add('vnp-selected');
            lbl.style.boxShadow = '0 8px 24px rgba(11,118,209,0.06)';
            lbl.style.border = '1px solid rgba(11,118,209,0.12)';
          }
        }
      });
    } catch (e) { console.error('vnpay: radios init error', e); }
  }

  if (body.classList && body.classList.contains('vnpay-return')) {
    function getValueByLabelText(text) {
      var groups = document.querySelectorAll('.table-responsive .form-group');
      for (var i = 0; i < groups.length; i++) {
        var g = groups[i];
        if (g.textContent && g.textContent.indexOf(text) !== -1) {
          var labels = g.querySelectorAll('label');
          if (labels.length >= 2) return labels[1].textContent.trim();
        }
      }
      return '';
    }

    var txn = getValueByLabelText('Mã đơn hàng:') || getValueByLabelText('Mã đơn hàng') || '';
    var amt = getValueByLabelText('Số tiền:') || '';
    var info = getValueByLabelText('Nội dung thanh toán:') || '';

    var actionsWrap = document.createElement('div');
    actionsWrap.className = 'vnpay-return-actions';
    actionsWrap.style.display = 'flex';
    actionsWrap.style.gap = '8px';
    actionsWrap.style.marginTop = '14px';
    actionsWrap.style.justifyContent = 'flex-end';

    var btnPrint = document.createElement('button');
    btnPrint.className = 'btn btn-primary';
    btnPrint.type = 'button';
    btnPrint.textContent = 'In biên lai';
    btnPrint.addEventListener('click', function () { window.print(); });

    var btnDownload = document.createElement('button');
    btnDownload.className = 'btn btn-secondary';
    btnDownload.type = 'button';
    btnDownload.textContent = 'Tải biên lai';
    btnDownload.addEventListener('click', function () {
      var content = 'Mã đơn hàng: ' + txn + '\nSố tiền: ' + amt + '\nNội dung: ' + info + '\nURL: ' + location.href + '\nThời gian: ' + new Date().toLocaleString();
      var blob = new Blob([content], { type: 'text/plain' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = (txn || 'vnpay_receipt') + '.txt';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    });

    var btnCopy = document.createElement('button');
    btnCopy.className = 'btn btn-link';
    btnCopy.type = 'button';
    btnCopy.textContent = 'Sao chép mã đơn hàng';
    btnCopy.addEventListener('click', function () {
      if (!navigator.clipboard) { alert('Trình duyệt không hỗ trợ clipboard API'); return; }
      navigator.clipboard.writeText(txn).then(function () { alert('Đã sao chép mã đơn hàng'); }, function () { alert('Không thể sao chép'); });
    });

    actionsWrap.appendChild(btnPrint);
    actionsWrap.appendChild(btnDownload);
    actionsWrap.appendChild(btnCopy);

    var tableResp = document.querySelector('.table-responsive');
    if (tableResp) tableResp.appendChild(actionsWrap);
  }
});
