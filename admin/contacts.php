<?php
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Pesan Masuk';

// Hapus pesan
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM contacts WHERE id=$delId");
    header('Location: contacts.php');
    exit;
}

$contacts = [];
$res = mysqli_query($koneksi, "SELECT * FROM contacts ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($res)) $contacts[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk — FrameSmile Admin</title>
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
                <h1 class="page-title">Pesan Masuk</h1>
                <p class="page-sub"><?= count($contacts) ?> pesan ditemukan</p>
            </div>
        </div>

        <?php if (empty($contacts)): ?>
            <div class="empty-state">
                <div style="font-size:48px;">📭</div>
                <p style="margin-top:12px;">Belum ada pesan masuk.</p>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($c['email']) ?>"
                               style="color:var(--accent);text-decoration:none;">
                                <?= htmlspecialchars($c['email']) ?>
                            </a>
                        </td>
                        <td style="max-width:300px;">
                            <?= htmlspecialchars($c['message']) ?>
                        </td>
                        <td style="white-space:nowrap;font-size:11px;">
                            <?= date('d M Y', strtotime($c['created_at'])) ?><br>
                            <span style="color:var(--gray);">
                                <?= date('H:i', strtotime($c['created_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?delete=<?= $c['id'] ?>"
                               class="tbl-btn tbl-btn-del"
                               onclick="return confirm('Hapus pesan ini?')">🗑</a>
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