<?php
require_once __DIR__ . '/../includes/db.php';

$products = [];
$res = mysqli_query($koneksi, "SELECT * FROM products WHERE is_active=1 ORDER BY sort_order ASC");
while ($row = mysqli_fetch_assoc($res)) $products[] = $row;

$adminWA = '6281234567890'; // ← ganti nomor WA admin
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/product.css">
    <title>Frame Smile | Home</title>
</head>

<body>

    <!--Navbar-->
    <?php require_once __DIR__ . '/../components/navbar.php'; ?>

    <main class="product-page">

      <!-- 1. Hero -->
      <section class="product-hero">
        <h1>Our Products</h1>
        <p>Cetak Kenangan indah dengan photostrip custom pilihan kamu</p>
      </section>

      <!-- 2. Paket -->
      <section class="product-packages">
        <h2>Pilih Paket</h2>

       <div class="packages-grid">
          <?php foreach ($products as $p):
              $harga = number_format($p['price'], 0, ',', '.');
              $waMsg = urlencode('Halo! Saya ingin pesan paket ' . $p['name'] . ' di FrameSmile 📸');
          ?>
          <div class="package-card">
              <?php if ($p['image']): ?>
                  <img src="/framesmile/<?= htmlspecialchars($p['image']) ?>"
                      alt="<?= htmlspecialchars($p['name']) ?>">
              <?php else: ?>
                  <div style="height:200px;background:#f4f4f4;display:flex;align-items:center;justify-content:center;color:#ccc;">No Image</div>
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
          <?php endforeach; ?>
      </div>

      </section>

      <!-- 3. Template Preview -->
      <section class="template-preview">
        <h2>koleksi Template</h2>
        <p>Beberapa contoh template yang tersedia</p>

        <div class="template-grid">
           <article class="template-card">
                <img src="../public/assets/product-assets/images/templates/template-1.png" alt="Template 1" loading="lazy">
            </article>
            <article class="template-card">
                <img src="../public/assets/product-assets/images/templates/template-2.png" alt="Template 2" loading="lazy">
            </article>
            <article class="template-card">
                <img src="../public/assets/product-assets/images/templates/template-3.png" alt="Template 3" loading="lazy">
            </article>
            <article class="template-card">
                <img src="../public/assets/product-assets/images/templates/template-4.png" alt="Template 4" loading="lazy">
            </article>
            <article class="template-card">
                <img src="../public/assets/product-assets/images/templates/template-5.png" alt="Template 5" loading="lazy">
            </article>
        </div>
        <p class="template-note">Dan masih banyak template lainnya — tanyakan ke admin!</p>
      </section>

      <!-- 4. Alur Pesan -->
      <section class="how-to-order">
        <h2>Cara Pesan</h2>

        <div class="steps-grid">
          <div class="step">
            <div class="step-number">1</div>
            <h4>Pilih Paket</h4>
            <p>Pilih paket Custom atau Template sesuai kebutuhanmu</p>
          </div>
          <div class="step">
            <div class="step-number">2</div>
            <h4>Chat Admin</h4>
            <p>Diskusikan desain, kirim foto, dan pilih template via WhatsApp</p>
          </div>
          <div class="step">
            <div class="step-number">3</div>
            <h4>Bayar & Terima</h4>
            <p>Lakukan pembayaran dan photostrip siap dicetak & dikirim</p>
          </div>
        </div>
      </section>

      <!-- 5. CTA -->
      <section class="product-cta">
        <h2>Siap Cetak Kenangan?</h2>
        <p>Hubungi admin sekarang dan wujudkan photostrip impianmu</p>
        <a href="https://wa.me/<?= $adminWA ?>?text=<?= urlencode('Halo! Saya ingin tahu lebih lanjut tentang FrameSmile 📸') ?>" class="btn-wa btn-wa-large" target="_blank">Chat Admin Sekarang</a>
      </section>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>


</body>

</html>