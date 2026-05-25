<?php
require_once __DIR__ . '/../app/config/database.php';

// Pisahkan query berdasarkan type
$customProducts   = [];
$templateProducts = [];

$res = mysqli_query($koneksi, "SELECT * FROM products WHERE is_active=1 ORDER BY sort_order ASC");

// Ambil data bundles
$bundles = [];
$resBundles = mysqli_query($koneksi, "SELECT * FROM bundles WHERE is_active=1 ORDER BY sort_order ASC");
while ($row = mysqli_fetch_assoc($resBundles)) $bundles[] = $row;

while ($row = mysqli_fetch_assoc($res)) {
    if ($row['type'] === 'custom')   $customProducts[]   = $row;
    if ($row['type'] === 'template') $templateProducts[] = $row;
}

$adminWA = '628999496466';

// Helper render card
function renderCard($p, $adminWA) {
    $harga = number_format($p['price'], 0, ',', '.');
    $waMsg = urlencode('Halo! Saya ingin pesan paket ' . $p['name'] . ' di FrameSmile 📸');
    ?>
    <div class="package-card">
        <?php if ($p['image']): ?>
            <img src="/framesmile/<?= htmlspecialchars($p['image']) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
            <div class="no-image">No Image</div>
        <?php endif; ?>
        <div class="package-info">
            <?php if ($p['badge']): ?>
                <span class="package-badge"><?= htmlspecialchars($p['badge']) ?></span>
            <?php endif; ?>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <p><?= htmlspecialchars($p['description']) ?></p>
            <div class="package-price">IDR <?= $harga ?></div>
            <a href="https://wa.me/<?= $adminWA ?>?text=<?= $waMsg ?>"
               class="btn-wa" target="_blank">
                Pesan Sekarang →
            </a>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/product.css">
    <title>Frame Smile | Produk</title>
</head>
<body>

    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main class="product-page">

        <!-- 1. Hero -->
        <section class="product-hero">
            <h1>Our Products</h1>
            <p>Cetak kenangan indah dengan photostrip pilihan kamu</p>
        </section>

        <!-- 2. Paket Custom -->
        <section class="product-packages product-packages--custom">
            <div class="packages-label">
                <span class="type-badge type-badge--custom">Custom</span>
                <h2>Paket Custom</h2>
                <p class="type-desc">Desain bebas dari nol — foto, warna, layout, dan teks semua bisa dikustom bareng admin.</p>
            </div>
            <div class="packages-grid">
                <?php foreach ($customProducts as $p): renderCard($p, $adminWA); endforeach; ?>
            </div>
        </section>

        <!-- 3. Paket Template -->
        <section class="product-packages product-packages--template">
            <div class="packages-label">
                <span class="type-badge type-badge--template">Template</span>
                <h2>Paket Template</h2>
                <p class="type-desc">Pilih dari koleksi template yang sudah jadi — cukup kirim foto, langsung cetak.</p>
            </div>
            <div class="packages-grid">
                <?php foreach ($templateProducts as $p): renderCard($p, $adminWA); endforeach; ?>
            </div>

            <!-- Template preview ada di dalam section template, bukan terpisah -->
            <div class="template-preview-inner">
                <h3>Koleksi Template</h3>
                <p>Beberapa contoh template yang tersedia</p>
                <div class="template-grid">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <article class="template-card">
                      <img src="../public/assets/product-assets/images/templates/template-<?= $i ?>.png" alt="Template <?= $i ?>" loading="lazy">
                      <div class="template-card-label">Template <?= $i ?></div>
                    </article>
                  <?php endfor; ?>
                </div>
                <p class="template-note">Dan masih banyak template lainnya — tanyakan ke admin!</p>
            </div>
        </section>

        <!-- BUNDLING -->
        <?php if (!empty($bundles)): ?>
        <section class="product-bundles">
            <div class="packages-label">
                <span class="type-badge type-badge--bundle">Bundling</span>
                <h2>Paket Bundling</h2>
                <p class="type-desc">Beli lebih banyak, bayar lebih hemat — cocok untuk acara atau koleksi bareng teman.</p>
            </div>

            <div class="bundles-grid">
                <?php foreach ($bundles as $b):
                    $hargaNormal = number_format($b['original_price'], 0, ',', '.');
                    $hargaBundle = number_format($b['bundle_price'],   0, ',', '.');
                    $hemat       = number_format($b['original_price'] - $b['bundle_price'], 0, ',', '.');
                    $waMsg       = urlencode('Halo! Saya ingin pesan ' . $b['name'] . ' di FrameSmile 📸');
                ?>
                <div class="bundle-card">
                    <?php if ($b['badge']): ?>
                        <span class="package-badge"><?= htmlspecialchars($b['badge']) ?></span>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($b['name']) ?></h3>
                    <p class="bundle-contents"><?= htmlspecialchars($b['contents']) ?></p>
                    <p class="bundle-desc"><?= htmlspecialchars($b['description']) ?></p>
                    <div class="bundle-pricing">
                        <span class="bundle-original">IDR <?= $hargaNormal ?></span>
                        <span class="bundle-price">IDR <?= $hargaBundle ?></span>
                        <span class="bundle-save">Hemat Rp <?= $hemat ?></span>
                    </div>
                    <a href="https://wa.me/<?= $adminWA ?>?text=<?= $waMsg ?>"
                      class="btn-wa" target="_blank">Pesan Sekarang →</a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- 4. Cara Pesan — PER TIPE -->
        <section class="how-to-order">
            <h2>Cara Pesan</h2>
            <p class="how-to-subtitle">Alurnya sedikit berbeda tergantung paket yang kamu pilih</p>

            <div class="how-to-columns">

                <!-- Kolom Custom -->
                <div class="how-to-col how-to-col--custom">
                    <h3>✦ Paket Custom</h3>
                    <ol class="steps-list">
                        <li>
                            <span class="step-number">1</span>
                            <div>
                                <h4>Pilih Paket Custom</h4>
                                <p>Klik "Pesan Sekarang" pada paket Custom di atas</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-number">2</span>
                            <div>
                                <h4>Diskusi Desain</h4>
                                <p>Ceritakan konsep ke admin — warna, layout, teks, dan kirim foto kamu via WhatsApp</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-number">3</span>
                            <div>
                                <h4>Review & Revisi</h4>
                                <p>Admin kirim preview desain, kamu bisa minta revisi sampai sesuai</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-number">4</span>
                            <div>
                                <h4>Bayar & Terima</h4>
                                <p>Lakukan pembayaran, photostrip dicetak dan siap dikirim</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Kolom Template -->
                <div class="how-to-col how-to-col--template">
                    <h3>◈ Paket Template</h3>
                    <ol class="steps-list">
                        <li>
                            <span class="step-number">1</span>
                            <div>
                                <h4>Pilih Template</h4>
                                <p>Lihat koleksi template di atas, catat nomor template yang kamu suka</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-number">2</span>
                            <div>
                                <h4>Chat Admin</h4>
                                <p>Klik "Pesan Sekarang", sebutkan nomor template dan kirim foto kamu</p>
                            </div>
                        </li>
                        <li>
                            <span class="step-number">3</span>
                            <div>
                                <h4>Bayar & Terima</h4>
                                <p>Lakukan pembayaran, photostrip langsung dicetak dan dikirim</p>
                            </div>
                        </li>
                    </ol>
                </div>

            </div>
        </section>

        <!-- 5. CTA -->
        <section class="product-cta">
            <h2>Siap Cetak Kenangan?</h2>
            <p>Hubungi admin sekarang dan wujudkan photostrip impianmu</p>
            <a href="https://wa.me/<?= $adminWA ?>?text=<?= urlencode('Halo! Saya ingin tahu lebih lanjut tentang FrameSmile 📸') ?>"
               class="btn-wa btn-wa-large" target="_blank">
                Chat Admin Sekarang
            </a>
        </section>

    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>
    <script src="../public/assets/js/hamburger.js"></script>
    <script src="../public/assets/js/navbar-css.js"></script>

  </body>
</html>