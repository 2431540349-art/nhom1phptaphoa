<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Quay lại trang chủ
header("Location: index.php");
exit;
?>
