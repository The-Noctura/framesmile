<?php
// admin/index.php — Dashboard admin
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

// Statistik ringkas
$totalTemplates = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM templates"))[0];
$totalProducts  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM products"))[0];
$totalOrders    = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM orders WHERE 1"))[0] ?? 0;
$pendingOrders  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM orders WHERE status='pending'"))[0] ?? 0;

// Order terbaru
$recentOrders = [];
$res = mysqli_query($koneksi, "
    SELECT id, customer_name, product_name, template_name, status, created_at
    FROM orders ORDER BY created_at DESC LIMIT 5
");
if ($res) while ($row = mysqli_fetch_assoc($res)) $recentOrders[] = $row;
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
      <h1 class="page-title">Dashboard</h1>
      <p class="page-sub">Selamat datang di panel admin FrameSmile</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🎨</div>
        <div class="stat-val"><?= $totalTemplates ?></div>
        <div class="stat-label">Total Template</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-val"><?= $totalProducts ?></div>
        <div class="stat-label">Total Produk</div>
      </div>
      <div class="stat-card accent">
        <div class="stat-icon">📋</div>
        <div class="stat-val"><?= $totalOrders ?></div>
        <div class="stat-label">Total Order</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-val"><?= $pendingOrders ?></div>
        <div class="stat-label">Order Pending</div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="section-header">
      <h2 class="section-title">Quick Action</h2>
    </div>
    <div class="quick-actions">
      <a href="templates.php" class="qa-btn">
        <span class="qa-icon">＋</span>
        Upload Template Baru
      </a>
      <a href="orders.php" class="qa-btn qa-sec">
        <span class="qa-icon">📋</span>
        Lihat Semua Order
      </a>
    </div>

    <!-- Recent orders -->
    <?php if (!empty($recentOrders)): ?>
    <div class="section-header" style="margin-top:32px;">
      <h2 class="section-title">Order Terbaru</h2>
      <a href="orders.php" class="section-link">Lihat semua →</a>
    </div>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Produk</th>
            <th>Template</th>
            <th>Status</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><code>ORD-<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></code></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td><?= htmlspecialchars($o['template_name']) ?></td>
            <td><span class="status-badge status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
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
