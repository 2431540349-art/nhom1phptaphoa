if ($vnp_ResponseCode == "00") {
    $orderId = $vnp_TxnRef;
    $amount = $vnp_Amount / 100;
    $payTime = $vnp_PayDate;
    $transactionNo = $vnp_TransactionNo ?? '';
    
    $customerEmail = $_SESSION['user_email'] ?? '';
    
    if (empty($customerEmail)) {
        $customerEmail = 'customer@example.com';
    }

    require_once('mail_config.php');
    
    $emailSent = sendPaymentConfirmationEmail(
        $customerEmail,
        $orderId,
        $amount,
        $payTime,
        $transactionNo
    );
    
    if ($emailSent) {
    }
}
