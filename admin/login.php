<?php
// admin/login.php
session_start();

// Kalau sudah login, langsung ke dashboard
if (!empty($_SESSION['fs_admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    // ← Ganti password di sini
    define('ADMIN_PASSWORD', 'framesmile2024');

    if ($pass === ADMIN_PASSWORD) {
        $_SESSION['fs_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — FrameSmile</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --black: #111; --white: #fff; --bg: #f4f4f4;
      --accent: #FF7979; --accent-hover: #e86060;
      --light: #e8e8e8; --gray: #888; --radius: 6px;
      --font-head: 'Montserrat', sans-serif;
      --font-body: 'Poppins', sans-serif;
    }
    body {
      font-family: var(--font-body);
      background: var(--bg);
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
    }
    .login-box {
      background: var(--white);
      border: 2px solid var(--black);
      border-radius: var(--radius);
      padding: 40px 36px;
      width: 380px;
      box-shadow: 6px 6px 0 var(--black);
    }
    .login-logo {
      font-family: var(--font-head);
      font-size: 22px; font-weight: 800;
      margin-bottom: 4px; color: var(--black);
    }
    .login-logo em { color: var(--accent); font-style: normal; }
    .login-sub {
      font-size: 12px; color: var(--gray);
      margin-bottom: 28px;
    }
    .form-label {
      display: block;
      font-family: var(--font-head);
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px;
      margin-bottom: 6px; color: var(--black);
    }
    .form-input {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid var(--black);
      border-radius: var(--radius);
      font-family: var(--font-body); font-size: 13px;
      background: var(--white); margin-bottom: 16px;
      transition: border-color .15s;
    }
    .form-input:focus { outline: none; border-color: var(--accent); }
    .btn-login {
      width: 100%; padding: 11px;
      background: var(--accent); color: var(--white);
      border: 2px solid var(--accent);
      border-radius: var(--radius);
      font-family: var(--font-head); font-size: 13px;
      font-weight: 700; cursor: pointer; transition: .15s;
      letter-spacing: .3px;
    }
    .btn-login:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    .error-msg {
      background: #fff0f0; border: 1px solid var(--accent);
      border-radius: var(--radius);
      color: var(--accent); font-size: 12px;
      padding: 8px 12px; margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-logo">Frame<em>Smile</em></div>
    <div class="login-sub">Admin Panel — masuk untuk kelola template</div>

    <?php if ($error): ?>
      <div class="error-msg">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label class="form-label">Password</label>
      <input class="form-input" type="password" name="password"
             placeholder="Masukkan password admin" autofocus required>
      <button class="btn-login" type="submit">Masuk →</button>
    </form>
  </div>
</body>
</html>
