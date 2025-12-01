<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * 
 *
 * @author CTT VNPAY
 */
require_once("./config.php");

$src = $_POST && count($_POST) ? $_POST : $_GET;

$vnp_TxnRef = isset($src['txn_ref']) && $src['txn_ref'] !== '' ? $src['txn_ref'] : rand(1,10000); 
$vnp_Amount = isset($src['amount']) ? floatval($src['amount']) : 0; 
$vnp_Locale = isset($src['language']) && $src['language'] !== '' ? $src['language'] : 'vn'; 
$vnp_BankCode = isset($src['bankCode']) ? $src['bankCode'] : ''; 
$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; 

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => ($vnp_Amount * 100),
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => "Thanh toan GD:" . $vnp_TxnRef,
    "vnp_OrderType" => "other",
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate"=>$expire
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}
header('Location: ' . $vnp_Url);
die();

