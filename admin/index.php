<?php
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$totalProducts = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM products WHERE is_active=1"))[0]

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — FrameSmile Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="admin-main">
    <?php include 'topbar.php'; ?>
    <div class="admin-content">

        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-sub">Selamat datang di panel admin FrameSmile</p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card accent">
                <div class="stat-icon">📦</div>
                <div class="stat-val"><?= $totalProducts ?></div>
                <div class="stat-label">Produk Aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✉️</div>
                <div class="stat-val"><?= $totalContacts ?></div>
                <div class="stat-label">Pesan Masuk</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>