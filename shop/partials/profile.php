<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/helpers.php'; 

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'index.php?page=8';
    header('Location: login.php');
    exit;
}

$userId = intval($_SESSION['user_id']);
$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '') {
        $errors[] = 'Vui lòng điền tên và email.';
    }

    $changePwd = false;
    if ($new !== '' || $confirm !== '' || $current !== '') {
        $changePwd = true;
        if ($new === '' || $confirm === '' || $current === '') {
            $errors[] = 'Để đổi mật khẩu, hãy điền mật khẩu hiện tại, mật khẩu mới và xác nhận.';
        } elseif ($new !== $confirm) {
            $errors[] = 'Mật khẩu mới và xác nhận không khớp.';
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT password_hash FROM user_custom WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if ($changePwd) {
            if (!$row || !password_verify($current, $row['password_hash'])) {
                $errors[] = 'Mật khẩu hiện tại không đúng.';
            }
        }
    }

    if (empty($errors)) {
        if ($changePwd) {
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE user_custom SET name = ?, email = ?, password_hash = ? WHERE id = ?');
            $stmt->bind_param('sssi', $name, $email, $newHash, $userId);
        } else {
            $stmt = $conn->prepare('UPDATE user_custom SET name = ?, email = ? WHERE id = ?');
            $stmt->bind_param('ssi', $name, $email, $userId);
        }
        if ($stmt->execute()) {
            $msg = 'Cập nhật hồ sơ thành công.';
            $_SESSION['user_name'] = $name;
        } else {
            $errors[] = 'Lỗi khi cập nhật hồ sơ. Vui lòng thử lại.';
        }
        $stmt->close();
    }
}

$user = null;
$stmt = $conn->prepare('SELECT id, name, email FROM user_custom WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($res) $user = $res->fetch_assoc();
$stmt->close();

?>
<div class="profile-page">
    <h2>Hồ sơ của tôi</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="profile-form">
        <div class="form-group">
            <label>Tên</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <h4>Đổi mật khẩu (tùy chọn)</h4>
        <div class="form-group">
            <label>Mật khẩu hiện tại</label>
            <input type="password" name="current_password" autocomplete="current-password">
        </div>
        <div class="form-group">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label>Xác nhận mật khẩu mới</label>
            <input type="password" name="confirm_password" autocomplete="new-password">
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px;">
            <a class="btn" href="index.php">Hủy</a>
            <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
        </div>
    </form>
</div>
