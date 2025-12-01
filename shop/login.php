<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập</title>
  <link rel="stylesheet" href="outinuser.css">

</head>
<body>
  <div class="register-container">
      <h1>Đăng nhập</h1>
      <p>Vui lòng nhập thông tin để tiếp tục</p>

      <form action="process-login.php" method="POST">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="ten@email.com" required>
        </div>

        <div class="input-group password-group">
          <label for="password">Mật khẩu</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <span class="toggle-password" id="togglePassword">hiện</span>
          </div>
        </div>

        <button type="submit" class="btn">Đăng nhập</button>

        <div class="login">
          <p>Chưa có tài khoản? <a href="signup.html">Đăng ký ngay</a></p>
        </div>
      </form>
  </div>

  <script>
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('click', () => {
      const type = password.type === 'password' ? 'text' : 'password';
      password.type = type;
      toggle.textContent = type === 'password' ? 'hiện' : 'ẩn';
    });
  </script>
</body>
</html>
