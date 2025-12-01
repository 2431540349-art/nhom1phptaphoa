<?php

require_once('mail_config.php');

$customerEmail = 'nguyenkydank48@gmail.com'; 
$orderId = 'ORD-2025-12345';
$amount = 1500000;  
$payDate = '20250101120000';  
$transactionNo = 'VNP123456789';

$emailSent = sendPaymentConfirmationEmail(
    $customerEmail,
    $orderId,
    $amount,
    $payDate,
    $transactionNo
);

if ($emailSent) {
    echo "Email gửi thành công!";
} else {
    echo "Email gửi thất bại";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Payment Email Test</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; }
        form { background: #f5f5f5; padding: 20px; border-radius: 8px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #667eea; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #764ba2; }
        .success { background: #d4edda; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .error { background: #f8d7da; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>Payment Email Test Form</h1>
    
    <form method="POST">
        <label>Customer Email:</label>
        <input type="email" name="email" required placeholder="customer@example.com">
        
        <label>Order ID:</label>
        <input type="text" name="order_id" required placeholder="ORD-2025-001">
        
        <label>Amount (VND):</label>
        <input type="number" name="amount" required placeholder="1000000">
        
        <label>Transaction No:</label>
        <input type="text" name="transaction_no" placeholder="VNP123456789">
        
        <button type="submit" name="send_email">Send Test Email</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_email'])) {
        $email = $_POST['email'] ?? '';
        $orderId = $_POST['order_id'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        $transactionNo = $_POST['transaction_no'] ?? '';
        
        // Validate
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<div class="error">Invalid email format</div>';
        } elseif (empty($orderId) || $amount <= 0) {
            echo '<div class="error">Order ID and Amount required</div>';
        } else {
            require_once('mail_config.php');
            
            $result = sendPaymentConfirmationEmail(
                $email,
                $orderId,
                $amount,
                date('YmdHis'),
                $transactionNo
            );
            
            if ($result) {
                echo '<div class="success">✓ Email sent successfully! Check your inbox.</div>';
            } else {
                echo '<div class="error">✗ Failed to send email. Check logs/payment_emails.log</div>';
            }
        }
    }
    ?>
</body>
</html>
