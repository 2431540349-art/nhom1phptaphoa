<?php
/**
 * File: payment_helpers.php
 * Các hàm helper hỗ trợ xử lý thanh toán VNPAY và gửi email
 */

require_once(__DIR__ . '/Admin/config.php');

/**
 * Lấy email khách hàng từ order_id
 * Điều chỉnh câu query theo cấu trúc database của bạn
 * 
 * @param string $orderId Mã đơn hàng (vnp_TxnRef)
 * @return string Email hoặc rỗng nếu không tìm thấy
 */
function getCustomerEmailFromOrder($orderId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT u.email 
        FROM orders o 
        JOIN user_custom u ON o.user_id = u.id 
        WHERE o.order_id = ?
    ");
    
    if (!$stmt) {
        error_log("Query error: " . $conn->error);
        return '';
    }
    
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['email'] ?? '';
    }
    
    $stmt->close();
    return '';
}

/**
 * Cập nhật trạng thái đơn hàng sau thanh toán
 * 
 * @param string $orderId Mã đơn hàng
 * @param string $status Trạng thái: 'paid', 'pending', 'failed', etc
 * @param string $paymentMethod Phương thức thanh toán: 'vnpay', 'cod', etc
 * @return bool True nếu cập nhật thành công
 */
function updateOrderPaymentStatus($orderId, $status = 'paid', $paymentMethod = 'vnpay') {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = ?, payment_method = ?, updated_at = NOW() 
        WHERE order_id = ?
    ");
    
    if (!$stmt) {
        error_log("Query error: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("sss", $status, $paymentMethod, $orderId);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        error_log("Order {$orderId} updated to status: {$status}");
    }
    
    return $result;
}

/**
 * Lưu thông tin giao dịch VNPAY vào database (tuỳ chọn)
 * 
 * @param array $paymentData Dữ liệu từ VNPAY
 * @return bool True nếu lưu thành công
 */
function saveVNPayTransaction($paymentData) {
    global $conn;
    
    $txnRef = $paymentData['vnp_TxnRef'] ?? '';
    $amount = ($paymentData['vnp_Amount'] ?? 0) / 100;
    $responseCode = $paymentData['vnp_ResponseCode'] ?? '';
    $transactionNo = $paymentData['vnp_TransactionNo'] ?? '';
    $bankCode = $paymentData['vnp_BankCode'] ?? '';
    $payDate = $paymentData['vnp_PayDate'] ?? '';
    
    $stmt = $conn->prepare("
        INSERT INTO vnpay_transactions 
        (order_id, amount, response_code, transaction_no, bank_code, pay_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    if (!$stmt) {
        error_log("Cannot create transaction table - " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("dsssss", $amount, $responseCode, $transactionNo, $bankCode, $payDate, $txnRef);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Gửi email thông báo cho admin khi có đơn hàng mới thanh toán
 * 
 * @param string $orderId Mã đơn hàng
 * @param float $amount Số tiền
 * @param string $customerEmail Email khách hàng
 * @return bool
 */
function sendAdminPaymentNotification($orderId, $amount, $customerEmail) {
    require_once(__DIR__ . '/mail_config.php');
    
    $adminEmail = 'admin@shoponline.vn'; 
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addAddress($adminEmail);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = '[THÔNG BÁO] Đơn hàng ' . $orderId . ' đã thanh toán';
        
        $formattedAmount = number_format($amount, 0, ',', '.');
        $mail->Body = "
            <h2>Thông báo: Đơn hàng mới thanh toán</h2>
            <p><b>Mã đơn hàng:</b> {$orderId}</p>
            <p><b>Số tiền:</b> {$formattedAmount} VND</p>
            <p><b>Email khách:</b> {$customerEmail}</p>
            <p><b>Thời gian:</b> " . date('d/m/Y H:i:s') . "</p>
            <p><a href='http://your_domain/Admin/order_details.php?id={$orderId}'>Xem chi tiết đơn hàng</a></p>
        ";

        return $mail->send();

    } catch (Exception $e) {
        error_log("Admin notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm helper: Kiểm tra xem đơn hàng đã được thanh toán hay chưa
 * 
 * @param string $orderId Mã đơn hàng
 * @return bool True nếu đã thanh toán
 */
function isOrderPaid($orderId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ? AND status = 'paid'");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result->num_rows > 0;
}

/**
 * Hàm helper: Lấy thông tin đơn hàng đầy đủ
 * 
 * @param string $orderId Mã đơn hàng
 * @return array|null
 */
function getOrderDetails($orderId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT o.*, u.email, u.name, u.phone, u.address 
        FROM orders o 
        JOIN user_custom u ON o.user_id = u.id 
        WHERE o.order_id = ?
    ");
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("s", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    return $order;
}

?>
