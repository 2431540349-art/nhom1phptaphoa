<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>VNPAY RESPONSE</title>
        <link href="/shop/vnpay_php/assets/bootstrap.css" rel="stylesheet">
        <link href="/shop/vnpay_php/assets/vnpay-style.css" rel="stylesheet">
        <script src="/shop/vnpay_php/assets/jquery-1.11.3.min.js"></script>
        <script src="/shop/vnpay_php/assets/vnpay.js"></script>
    </head>
    <body class="vnpay-return">
        <?php
        session_start();
        require_once("./config.php");
        require_once("../mail_config.php");
        
        $vnp_SecureHash = isset($_GET['vnp_SecureHash']) ? $_GET['vnp_SecureHash'] : '';
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        if (isset($inputData['vnp_SecureHash'])) unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        ?>
        <?php
        $txnRef = isset($_GET['vnp_TxnRef']) ? htmlspecialchars($_GET['vnp_TxnRef']) : '';
        $amountRaw = isset($_GET['vnp_Amount']) ? htmlspecialchars($_GET['vnp_Amount']) : '';
        $amountDisplay = $amountRaw !== '' ? $amountRaw : '';
        $orderInfo = isset($_GET['vnp_OrderInfo']) ? htmlspecialchars($_GET['vnp_OrderInfo']) : '';
        $responseCode = isset($_GET['vnp_ResponseCode']) ? $_GET['vnp_ResponseCode'] : '';
        $transactionNo = isset($_GET['vnp_TransactionNo']) ? htmlspecialchars($_GET['vnp_TransactionNo']) : '';
        $bankCode = isset($_GET['vnp_BankCode']) ? htmlspecialchars($_GET['vnp_BankCode']) : '';
        $payDate = isset($_GET['vnp_PayDate']) ? htmlspecialchars($_GET['vnp_PayDate']) : '';
        $isValidSig = (!empty($vnp_SecureHash) && $secureHash === $vnp_SecureHash);
        $isSuccess = ($isValidSig && $responseCode === '00');
        if ($isSuccess) {
            $customerEmail = $_SESSION['user_email'] ?? '';
            
            if (!empty($customerEmail)) {
                $amount = (float)$amountRaw / 100;
                sendPaymentConfirmationEmail(
                    $customerEmail,
                    $txnRef,
                    $amount,
                    $payDate,
                    $transactionNo
                );
            }
        }
        ?>

        <div class="vnp-wrap">
            <div class="vnp-hero vnp-card">
                <div class="vnp-status <?= $isSuccess ? 'success' : 'fail' ?>"><?= $isSuccess ? 'OK' : 'ERR' ?></div>
                <div class="vnp-hero-meta">
                    <h2>Thông tin giao dịch</h2>
                    <p class="muted">Mã đơn hàng: <strong><?= $txnRef ?></strong></p>
                    <div class="vnp-amount-hero">Số tiền: <?= $amountDisplay ?></div>
                    <p class="muted" style="margin-top:6px">Kết quả: <?= $isSuccess ? 'Giao dịch thành công' : 'Giao dịch không thành công' ?></p>
                </div>
            </div>

            <div class="vnp-summary">
                <div class="vnp-card vnp-details">
                    <div class="row"><div class="k">Mã đơn hàng</div><div class="v"><?= $txnRef ?></div></div>
                    <div class="row"><div class="k">Số tiền</div><div class="v"><?= $amountDisplay ?></div></div>
                    <div class="row"><div class="k">Nội dung</div><div class="v"><?= $orderInfo ?></div></div>
                    <div class="row"><div class="k">Mã phản hồi</div><div class="v"><?= htmlspecialchars($responseCode) ?></div></div>
                    <div class="row"><div class="k">Mã GD tại VNPAY</div><div class="v"><?= $transactionNo ?></div></div>
                    <div class="row"><div class="k">Mã ngân hàng</div><div class="v"><?= $bankCode ?></div></div>
                    <div class="row"><div class="k">Thời gian</div><div class="v"><?= $payDate ?></div></div>
                </div>

                <div>
                    <div class="vnp-card">
                        <?php if ($isSuccess): ?>
                            <div class="vnp-banner success">Giao dịch thành công</div>
                            <div class="vnp-actions">
                                <a href="/shop/index.php" class="btn btn-primary">Quay về trang chủ</a>
                                <button type="button" onclick="window.print()" class="btn btn-download">In biên lai</button>
                            </div>
                        <?php else: ?>
                            <div class="vnp-banner fail">Giao dịch không thành công</div>
                            <div class="vnp-actions">
                                <button id="btnBack" class="btn btn-ghost">Quay lại</button>
                                <a href="/shop/index.php" class="btn btn-primary">Trang chủ</a>
                            </div>
                            <?php
                            $returnAmount = isset($_GET['vnp_Amount']) ? (float)($_GET['vnp_Amount'])/100 : '';
                            $returnTxn = isset($_GET['vnp_TxnRef']) ? rawurlencode($_GET['vnp_TxnRef']) : '';
                            $redirectUrl = '/shop/index.php?page=5';
                            if ($returnAmount !== '') {
                                $redirectUrl .= '?amount=' . rawurlencode($returnAmount);
                                if ($returnTxn !== '') $redirectUrl .= '&txn_ref=' . $returnTxn;
                            }
                            ?>
                            <div style="margin-top:8px;color:var(--muted)">Bạn sẽ được chuyển về trang thanh toán trong 3 giây...</div>
                            <script>
                            document.addEventListener('DOMContentLoaded', function(){
                              var b = document.getElementById('btnBack');
                              if(b){ b.addEventListener('click',function(){ if(document.referrer && document.referrer.indexOf(location.origin)!==-1){ location.href = document.referrer; } else { history.back(); } }); }
                              setTimeout(function(){ try{ location.href = '<?= $redirectUrl ?>'; }catch(e){ console.error(e); } }, 3000);
                            });
                            </script>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <p>&nbsp;</p>
            <footer class="footer center muted">&copy; VNPAY <?php echo date('Y')?></footer>
        </div>  
    </body>
</html>
