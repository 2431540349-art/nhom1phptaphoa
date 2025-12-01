<?php
session_start();


require_once(__DIR__ . "/Admin/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';


    if (!$name || !$email || !$password || !$confirm) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.history.back();</script>";
        exit;
    }


    if ($password !== $confirm) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
        exit;
    }


    $stmt = $conn->prepare("SELECT id FROM user_custom WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo "<script>alert('Email này đã được đăng ký!'); window.history.back();</script>";
        exit;
    }
    $stmt->close();


    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO user_custom (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='login.php';</script>";
        exit;
    } else {
        $stmt->close();
        echo "<script>alert('Lỗi khi đăng ký. Vui lòng thử lại!'); window.history.back();</script>";
        exit;
    }
}
?>
