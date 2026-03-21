<?php
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Kelola Produk';
$baseUrl   = '/framesmile';
$uploadDir = __DIR__ . '/../public/assets/product-assets/images/products/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$success = '';
$error   = '';

// ── UPDATE PRODUK ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)$_POST['id'];
    $name        = mysqli_real_escape_string($koneksi, trim($_POST['name']        ?? ''));
    $description = mysqli_real_escape_string($koneksi, trim($_POST['description'] ?? ''));
    $price       = (int)$_POST['price'];
    $badge       = mysqli_real_escape_string($koneksi, trim($_POST['badge']       ?? ''));
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    // Upload gambar baru kalau ada
    $imagePath = mysqli_real_escape_string($koneksi, $_POST['current_image'] ?? '');
    if (!empty($_FILES['image']['name'])) {
        $file    = $_FILES['image'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg'];

        if (!in_array($ext, $allowed)) {
            $error = 'Format file harus PNG atau JPG!';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Ukuran file maksimal 5MB!';
        } else {
            $filename  = 'product_' . $id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $imagePath = mysqli_real_escape_string($koneksi, 'public/assets/product-assets/images/products/' . $filename);
            } else {
                $error = 'Gagal upload gambar.';
            }
        }
    }

    if (!$error) {
        mysqli_query($koneksi, "
            UPDATE products
            SET name='$name', description='$description', price=$price,
                badge='$badge', is_active=$is_active, image='$imagePath'
            WHERE id=$id
        ");
        $success = 'Produk berhasil diupdate!';
    }
}

// Ambil semua produk
$products = [];
$res = mysqli_query($koneksi, "SELECT * FROM products ORDER BY sort_order ASC");
while ($row = mysqli_fetch_assoc($res)) $products[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk — FrameSmile Admin</title>
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
                <h1 class="page-title">Kelola Produk</h1>
                <p class="page-sub">Update informasi produk yang tampil di halaman publik</p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="products-edit-list">
            <?php foreach ($products as $p): ?>
            <div class="product-edit-card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($p['image'] ?? '') ?>">

                    <div class="product-edit-header">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <label class="toggle">
                            <input type="checkbox" name="is_active" <?= $p['is_active'] ? 'checked' : '' ?>>
                            <span>Aktif</span>
                        </label>
                    </div>

                    <div class="product-edit-body">

                        <!-- Gambar -->
                        <div class="product-edit-img">
                            <?php if ($p['image']): ?>
                                <img src="<?= $baseUrl . '/' . htmlspecialchars($p['image']) ?>"
                                     alt="" id="preview_<?= $p['id'] ?>">
                            <?php else: ?>
                                <div class="img-placeholder">No Image</div>
                            <?php endif; ?>
                            <input type="file" name="image" accept=".png,.jpg,.jpeg"
                                   onchange="previewImg(this, 'preview_<?= $p['id'] ?>')">
                            <small>Kosongkan jika tidak ingin ganti gambar</small>
                        </div>

                        <!-- Form fields -->
                        <div class="product-edit-fields">
                            <div class="form-group">
                                <label class="form-label">Nama Paket</label>
                                <input class="form-input" type="text" name="name"
                                       value="<?= htmlspecialchars($p['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-input" name="description"
                                          rows="3"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Harga (IDR)</label>
                                    <input class="form-input" type="number" name="price"
                                           value="<?= $p['price'] ?? 0 ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Badge</label>
                                    <input class="form-input" type="text" name="badge"
                                           value="<?= htmlspecialchars($p['badge'] ?? '') ?>"
                                           placeholder="Populer, Baru, dll">
                                </div>
                            </div>
                            <button type="submit" class="btn-primary">Simpan Perubahan →</button>
                        </div>

                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
function previewImg(input, previewId) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById(previewId);
        if (img) img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>

</body>
</html>