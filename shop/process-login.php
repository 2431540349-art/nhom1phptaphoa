<?php
session_start();

require_once(__DIR__ . "/Admin/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo "<script>alert('Vui lòng nhập email và mật khẩu'); history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, password_hash FROM user_custom WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $email; 

        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); 
            header("Location: $redirect");
            exit;
        }

        header("Location: index.php");
        exit;
    }

    echo "<script>alert('Sai email hoặc mật khẩu!'); history.back();</script>";
}
?>
