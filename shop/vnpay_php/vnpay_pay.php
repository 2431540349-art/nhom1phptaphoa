<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Tạo mới đơn hàng</title>
        <link href="/shop/vnpay_php/assets/bootstrap.css" rel="stylesheet">
        <link href="/shop/vnpay_php/assets/vnpay-style.css" rel="stylesheet">
        <script src="/shop/vnpay_php/assets/jquery-1.11.3.min.js"></script>
        <script src="/shop/vnpay_php/assets/vnpay.js"></script>
    </head>

    <body class="vnpay-page">
        <?php
        require_once("./config.php");
        $prefillAmount = isset($_GET['amount']) ? floatval($_GET['amount']) : 10000;
        $txn_ref = isset($_GET['txn_ref']) ? trim($_GET['txn_ref']) : '';
        ?>

        <div class="vnp-wrap">
            <div class="vnp-card">
                <div class="vnpay-title">Tạo mới đơn hàng</div>
                <div class="vnp-sub">Nhập số tiền và chọn phương thức thanh toán phù hợp.</div>
                <form action="vnpay_create_payment.php" id="frmCreateOrder" method="post" class="vnp-form">
                    <div class="left">
                        <div class="vnp-amount">
                            <label for="amount">Số tiền</label>
                            <input id="amount" name="amount" type="number" min="1" max="100000000" value="<?= htmlspecialchars($prefillAmount) ?>" />
                            <input type="hidden" name="txn_ref" value="<?= htmlspecialchars($txn_ref) ?>">
                        </div>

                        <div style="margin-top:14px">
                            <label>Ngôn ngữ giao diện</label>
                            <div style="margin-top:8px">
                                <label style="margin-right:12px"><input type="radio" name="language" value="vn" checked> Tiếng Việt</label>
                                <label><input type="radio" name="language" value="en"> English</label>
                            </div>
                        </div>
                    </div>

                    <div class="right">
                        <h4 style="margin-top:0">Chọn phương thức thanh toán</h4>
                        <div class="vnp-methods">
                            <div>
                                <input type="radio" id="bank_none" name="bankCode" value="" checked>
                                <label for="bank_none" class="vnp-method">
                                    <span class="ico">QR</span>
                                    <span class="meta"><span class="title">Cổng VNPAYQR</span><br><span class="desc">Chuyển hướng sang cổng VNPAY để chọn phương thức</span></span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="bank_vnpayqr" name="bankCode" value="VNPAYQR">
                                <label for="bank_vnpayqr" class="vnp-method">
                                    <span class="ico">APP</span>
                                    <span class="meta"><span class="title">VNPAYQR (App)</span><br><span class="desc">Thanh toán bằng ứng dụng hỗ trợ VNPAYQR</span></span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="bank_vnbank" name="bankCode" value="VNBANK">
                                <label for="bank_vnbank" class="vnp-method">
                                    <span class="ico">ATM</span>
                                    <span class="meta"><span class="title">Thẻ nội địa / ATM</span><br><span class="desc">Thanh toán qua thẻ ATM hoặc tài khoản nội địa</span></span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="bank_intcard" name="bankCode" value="INTCARD">
                                <label for="bank_intcard" class="vnp-method">
                                    <span class="ico">CARD</span>
                                    <span class="meta"><span class="title">Thẻ quốc tế</span><br><span class="desc">Thanh toán qua thẻ Visa, MasterCard, ...</span></span>
                                </label>
                            </div>
                        </div>

                        <div class="vnp-pay-btn">
                            <button type="submit" class="btn btn-primary">Thanh toán</button>
                            <a href="/shop/index.php" class="btn btn-ghost">Hủy</a>
                        </div>
                    </div>
                </form>
            </div>
            <p>&nbsp;</p>
            <footer class="footer center muted">&copy; VNPAY 2020</footer>
        </div>
    </body>
</html>
