<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$vnp_TmnCode = "D6CJ2GT5"; 
$vnp_HashSecret = "ZF2QJ8P69VQJ06WODZF3FUOWBUWDMAPK"; //Secret key
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "https://ethelyn-nontransmittible-basically.ngrok-free.dev/shop/vnpay_php/vnpay_return.php";
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
$apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
