<?php
// admin/orders.php — kelola order masuk
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Kelola Order';
$baseUrl   = '/framesmile';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $ordId    = (int)$_POST['order_id'];
    $status   = mysqli_real_escape_string($koneksi, $_POST['status']);
    $allowed  = ['pending', 'processing', 'done'];
    if (in_array($status, $allowed)) {
        mysqli_query($koneksi, "UPDATE orders SET status='$status' WHERE id=$ordId");
    }
}

// Filter
$statusFilter = $_GET['status'] ?? 'all';
$where = $statusFilter !== 'all' ? "WHERE status = '" . mysqli_real_escape_string($koneksi, $statusFilter) . "'" : '';

$orders = [];
$res = mysqli_query($koneksi, "SELECT * FROM orders $where ORDER BY created_at DESC");
if ($res) while ($row = mysqli_fetch_assoc($res)) $orders[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders — FrameSmile Admin</title>
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
        <h1 class="page-title">Kelola Order</h1>
        <p class="page-sub"><?= count($orders) ?> order ditemukan</p>
      </div>
      <!-- Filter status -->
      <div style="display:flex; gap:6px;">
        <?php foreach (['all'=>'Semua','pending'=>'Pending','processing'=>'Proses','done'=>'Selesai'] as $val => $label): ?>
        <a href="?status=<?= $val ?>"
           class="tbl-btn <?= $statusFilter===$val ? 'tbl-btn-edit' : '' ?>"
           style="text-decoration:none;">
          <?= $label ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (empty($orders)): ?>
      <div class="empty-state">
        <div style="font-size:48px; margin-bottom:12px;">📭</div>
        <p>Belum ada order masuk.</p>
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID Order</th>
            <th>Nama</th>
            <th>WhatsApp</th>
            <th>Produk</th>
            <th>Template</th>
            <th>Preview</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
          <tr>
            <td><code>ORD-<?= str_pad($o['id'], 4, '0', STR_PAD_LEFT) ?></code></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td>
              <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$o['customer_phone']) ?>"
                 target="_blank" style="color:var(--accent); text-decoration:none;">
                <?= htmlspecialchars($o['customer_phone']) ?>
              </a>
            </td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td><?= htmlspecialchars($o['template_name']) ?></td>
            <td>
              <?php if ($o['image_path']): ?>
                <a href="<?= $baseUrl . '/' . htmlspecialchars($o['image_path']) ?>" target="_blank">
                  <img src="<?= $baseUrl . '/' . htmlspecialchars($o['image_path']) ?>"
                       style="width:48px; height:48px; object-fit:cover; border-radius:4px; border:1px solid #e8e8e8;">
                </a>
              <?php else: ?>
                <span style="color:#ccc; font-size:11px;">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="status-badge status-<?= $o['status'] ?>">
                <?= ucfirst($o['status']) ?>
              </span>
            </td>
            <td style="white-space:nowrap; font-size:11px;">
              <?= date('d M Y', strtotime($o['created_at'])) ?><br>
              <span style="color:var(--gray);"><?= date('H:i', strtotime($o['created_at'])) ?></span>
            </td>
            <td>
              <form method="POST" style="display:flex; gap:4px; align-items:center;">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" class="form-input" style="padding:4px 6px; font-size:11px; width:auto;">
                  <option value="pending"    <?= $o['status']==='pending'    ?'selected':'' ?>>Pending</option>
                  <option value="processing" <?= $o['status']==='processing' ?'selected':'' ?>>Proses</option>
                  <option value="done"       <?= $o['status']==='done'       ?'selected':'' ?>>Selesai</option>
                </select>
                <button type="submit" name="update_status" class="tbl-btn tbl-btn-edit">✓</button>
              </form>
            </td>
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
