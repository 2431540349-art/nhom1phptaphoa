<?php
/**
 * Cấu hình gửi mail sử dụng PHPMailer
 */

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', 'nguyenkydank48@gmail.com');
define('MAIL_PASSWORD', 'rgkbujlegekvnghl'); 
define('MAIL_PORT', 465);
define('MAIL_ENCRYPTION', 'ssl');
define('MAIL_FROM_NAME', 'Shop Online');

/**
 * Hàm gửi mail xác nhận thanh toán
 * @param string $customerEmail Email khách hàng
 * @param string $orderId Mã đơn hàng
 * @param float $amount Số tiền thanh toán
 * @param string $payDate Ngày thanh toán
 * @param string $transactionNo Mã giao dịch VNPAY
 * @return bool True nếu gửi thành công, False nếu thất bại
 */
function sendPaymentConfirmationEmail($customerEmail, $orderId, $amount, $payDate, $transactionNo = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addAddress($customerEmail);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Xác nhận thanh toán thành công - Đơn hàng #' . $orderId;
        
        $formattedDate = formatPaymentDate($payDate);
        $formattedAmount = number_format($amount, 0, ',', '.');
        
        $mail->Body = buildPaymentEmailContent($orderId, $formattedAmount, $formattedDate, $transactionNo);

        $mail->send();
        
        logPaymentEmail($customerEmail, $orderId, true);
        
        return true;

    } catch (Exception $e) {
        $errorMsg = "Error: " . $e->getMessage() . " | PHPMailer: " . $mail->ErrorInfo;
        error_log("Lỗi gửi mail thanh toán cho {$customerEmail}: " . $errorMsg);
        logPaymentEmail($customerEmail, $orderId, false, $errorMsg);
        
        return false;
    }
}

/**
 * Hàm định dạng ngày giờ từ VNPAY (YYYYMMDDHHmmss)
 * @param string $payDate Ngày từ VNPAY format YYYYMMDDHHmmss
 * @return string Ngày định dạng: d/m/Y H:i
 */
function formatPaymentDate($payDate) {
    if (strlen($payDate) === 14) {
        $year = substr($payDate, 0, 4);
        $month = substr($payDate, 4, 2);
        $day = substr($payDate, 6, 2);
        $hour = substr($payDate, 8, 2);
        $minute = substr($payDate, 10, 2);
        $second = substr($payDate, 12, 2);
        
        return "$day/$month/$year $hour:$minute:$second";
    }
    return $payDate;
}

/**
 * Hàm tạo nội dung HTML email
 * @param string $orderId Mã đơn hàng
 * @param string $amount Số tiền định dạng
 * @param string $date Ngày thanh toán định dạng
 * @param string $transactionNo Mã giao dịch
 * @return string Nội dung HTML
 */
function buildPaymentEmailContent($orderId, $amount, $date, $transactionNo = '') {
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
            .header h1 { margin: 0; font-size: 28px; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .info-box { background: white; padding: 20px; margin: 15px 0; border-left: 4px solid #667eea; border-radius: 4px; }
            .info-row { display: flex; justify-content: space-between; margin: 12px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
            .info-label { font-weight: bold; color: #666; }
            .info-value { color: #333; text-align: right; }
            .amount { font-size: 24px; color: #27ae60; font-weight: bold; text-align: center; padding: 20px; background: #f0fdf4; border-radius: 4px; margin: 20px 0; }
            .footer { text-align: center; color: #888; font-size: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; }
            .success-badge { display: inline-block; background: #27ae60; color: white; padding: 8px 15px; border-radius: 20px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>✓ Thanh toán thành công</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px;'>Qua cổng thanh toán VNPAY</p>
            </div>
            
            <div class='content'>
                <p style='font-size: 16px; color: #333;'>Xin chào khách hàng!</p>
                <p>Cảm ơn bạn đã thanh toán cho đơn hàng của mình. Dưới đây là thông tin chi tiết giao dịch:</p>
                
                <div class='info-box'>
                    <div class='info-row'>
                        <span class='info-label'>Mã đơn hàng:</span>
                        <span class='info-value'><strong>{$orderId}</strong></span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Ngày thanh toán:</span>
                        <span class='info-value'>{$date}</span>
                    </div>
                    " . (!empty($transactionNo) ? "
                    <div class='info-row'>
                        <span class='info-label'>Mã giao dịch:</span>
                        <span class='info-value'>{$transactionNo}</span>
                    </div>
                    " : "") . "
                </div>
                
                <div class='amount'>
                    Số tiền: <span style='font-size: 28px;'>{$amount}</span> VND
                </div>
                
                <p style='background: #f0f8ff; padding: 15px; border-radius: 4px; border-left: 4px solid #2196F3;'>
                    <strong>📦 Trạng thái đơn hàng:</strong> Đơn hàng của bạn đã được xác nhận. Chúng tôi sẽ chuẩn bị và gửi hàng cho bạn trong thời gian sớm nhất.
                </p>
                
                <p style='margin-top: 20px;'>
                    Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ với chúng tôi:
                </p>
                
                <p style='text-align: center; color: #667eea;'>
                    <strong>Email:</strong> support@shoponline.vn<br>
                    <strong>Hotline:</strong> 1900-xxxx<br>
                    <strong>Thời gian hỗ trợ:</strong> 8:00 - 22:00 hàng ngày
                </p>
                
                <div class='footer'>
                    <p>Đây là email tự động, vui lòng không trả lời email này.</p>
                    <p>&copy; 2025 Shop Online. Bảo lưu mọi quyền.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return $html;
}

/**
 * Hàm ghi log email vào file hoặc database
 * @param string $email Email khách hàng
 * @param string $orderId Mã đơn hàng
 * @param bool $success Có gửi thành công không
 * @param string $errorMsg Thông báo lỗi nếu có
 */
function logPaymentEmail($email, $orderId, $success = true, $errorMsg = '') {
    $logFile = __DIR__ . '/logs/payment_emails.log';
    
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    
    $status = $success ? 'SUCCESS' : 'FAILED';
    $timestamp = date('Y-m-d H:i:s');
    $errorInfo = !empty($errorMsg) ? " | Error: {$errorMsg}" : '';
    
    $logMessage = "[{$timestamp}] [{$status}] Email: {$email} | Order: {$orderId}{$errorInfo}\n";
    
    error_log($logMessage, 3, $logFile);
}

?>
