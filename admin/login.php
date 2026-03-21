<?php
session_start();

if (!empty($_SESSION['fs_admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    define('ADMIN_PASSWORD', 'framesmile2024'); // ← ganti password di sini

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
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">Frame<em>Smile</em></div>
        <div class="login-sub">Admin Panel</div>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Password</label>
            <input class="form-input" type="password" name="password"
                   placeholder="Masukkan password admin" autofocus required>
            <button class="btn-primary" type="submit" style="width:100%;margin-top:4px;">
                Masuk →
            </button>
        </form>
    </div>
</body>
</html>