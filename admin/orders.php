<?php
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Kelola Order';
$success   = '';
$error     = '';

// ── TAMBAH ORDER ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name      = mysqli_real_escape_string($koneksi, trim($_POST['customer_name']  ?? ''));
    $phone     = mysqli_real_escape_string($koneksi, trim($_POST['customer_phone'] ?? ''));
    $productId = (int)$_POST['product_id'];
    $note      = mysqli_real_escape_string($koneksi, trim($_POST['note']           ?? ''));

    if (!$name || !$phone || !$productId) {
        $error = 'Nama, nomor WA, dan produk wajib diisi!';
    } else {
        $product     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT name FROM products WHERE id=$productId"));
        $productName = mysqli_real_escape_string($koneksi, $product['name'] ?? '');
        mysqli_query($koneksi, "
            INSERT INTO orders (customer_name, customer_phone, product_id, product_name, note)
            VALUES ('$name', '$phone', $productId, '$productName', '$note')
        ");
        $success = 'Order berhasil ditambahkan!';
    }
}

// ── UPDATE STATUS ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $orderId = (int)$_POST['order_id'];
    $status  = mysqli_real_escape_string($koneksi, $_POST['status']);
    $allowed = ['pending', 'processing', 'done'];
    if (in_array($status, $allowed)) {
        mysqli_query($koneksi, "UPDATE orders SET status='$status' WHERE id=$orderId");
        $success = 'Status order diupdate!';
    }
}

// ── HAPUS ORDER ──
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM orders WHERE id=$delId");
    header('Location: orders.php');
    exit;
}

// Ambil produk untuk dropdown
$products = [];
$res = mysqli_query($koneksi, "SELECT id, name FROM products WHERE is_active=1 ORDER BY sort_order");
while ($row = mysqli_fetch_assoc($res)) $products[] = $row;

// Ambil semua order
$statusFilter = $_GET['status'] ?? 'all';
$where = $statusFilter !== 'all' ? "WHERE status='" . mysqli_real_escape_string($koneksi, $statusFilter) . "'" : '';
$orders = [];
$res = mysqli_query($koneksi, "SELECT * FROM orders $where ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($res)) $orders[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Order — FrameSmile Admin</title>
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
            <button class="btn-primary" onclick="document.getElementById('addModal').classList.add('open')">
                ＋ Tambah Order
            </button>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Filter status -->
        <div style="display:flex; gap:6px; margin-bottom:16px;">
            <?php foreach (['all'=>'Semua','pending'=>'Pending','processing'=>'Proses','done'=>'Selesai'] as $val => $label): ?>
            <a href="?status=<?= $val ?>"
               class="tbl-btn <?= $statusFilter===$val ? 'tbl-btn-active' : '' ?>"
               style="text-decoration:none;">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div style="font-size:48px;">📭</div>
                <p style="margin-top:12px;">Belum ada order.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>WhatsApp</th>
                        <th>Produk</th>
                        <th>Catatan</th>
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
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o['customer_phone']) ?>"
                               target="_blank" style="color:var(--accent);text-decoration:none;">
                                <?= htmlspecialchars($o['customer_phone']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($o['product_name']) ?></td>
                        <td style="max-width:200px;font-size:11px;">
                            <?= htmlspecialchars($o['note'] ?: '—') ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $o['status'] ?>">
                                <?= ucfirst($o['status']) ?>
                            </span>
                        </td>
                        <td style="white-space:nowrap;font-size:11px;">
                            <?= date('d M Y', strtotime($o['created_at'])) ?><br>
                            <span style="color:var(--gray);"><?= date('H:i', strtotime($o['created_at'])) ?></span>
                        </td>
                        <td>
                            <form method="POST" style="display:flex;gap:4px;align-items:center;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                <select name="status" class="form-input"
                                        style="padding:4px 6px;font-size:11px;width:auto;">
                                    <option value="pending"    <?= $o['status']==='pending'    ?'selected':'' ?>>Pending</option>
                                    <option value="processing" <?= $o['status']==='processing' ?'selected':'' ?>>Proses</option>
                                    <option value="done"       <?= $o['status']==='done'       ?'selected':'' ?>>Selesai</option>
                                </select>
                                <button type="submit" class="tbl-btn">✓</button>
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

<!-- ── MODAL TAMBAH ORDER ── -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Tambah Order Manual</h2>
            <button class="modal-close"
                    onclick="document.getElementById('addModal').classList.remove('open')">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Customer <span style="color:var(--accent)">*</span></label>
                    <input class="form-input" type="text" name="customer_name"
                           placeholder="Nama customer" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp <span style="color:var(--accent)">*</span></label>
                    <input class="form-input" type="text" name="customer_phone"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label class="form-label">Produk <span style="color:var(--accent)">*</span></label>
                    <select class="form-input" name="product_id" required>
                        <option value="">Pilih produk...</option>
                        <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-input" name="note" rows="3"
                              placeholder="Detail desain, ukuran, dll"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sec"
                        onclick="document.getElementById('addModal').classList.remove('open')">Batal</button>
                <button type="submit" class="btn-primary">Tambah Order →</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>