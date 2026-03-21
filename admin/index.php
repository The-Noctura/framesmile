<?php
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$totalProducts = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM products WHERE is_active=1"))[0];
$totalContacts = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM contacts"))[0];

$recentContacts = [];
$res = mysqli_query($koneksi, "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($res)) $recentContacts[] = $row;
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

        <?php if (!empty($recentContacts)): ?>
        <div class="section-header" style="margin-top:32px;">
            <h2 class="section-title">Pesan Terbaru</h2>
            <a href="contacts.php" class="section-link">Lihat semua →</a>
        </div>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentContacts as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars(substr($c['message'], 0, 60)) ?>...</td>
                        <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>