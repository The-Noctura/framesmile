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

          <div class="package-card">
            <img src="../public/assets/product-assets/images/products/product-1.png" alt="Custom Photostrip">
            <div class="package-info">
              <h3>Custom</h3>
              <p>Desain bebas sesuka hati — foto, warna, layout, teks semua bisa dikustom langsung bareng admin via WhatsApp.</p>
              <div class="package-price">IDR 15.000</div>
              <ul class="package-includes">
                <li>Editing photostrip custom</li>
                <li>Bebas request desain</li>
                <li>Cetak foto online</li>
              </ul>
               <a href="https://wa.me/6281234567890?text=Halo!%20Saya%20ingin%20pesan%20paket%20Custom%20Photostrip%20%F0%9F%93%B8" class="btn-wa" target="_blank">Pesan Sekarang</a>
            </div>
          </div>

          <div class="package-card">
            <img src="../public/assets/product-assets/images/products/product-2.png" alt="Template Photostrip">
            <div class="package-info">
              <h3>Template</h3>
              <p>Pilih template yang sudah tersedia, tinggal kirim foto kamu ke admin via WhatsApp — cepat dan mudah.</p>
              <div class="package-price">IDR 10.000</div>
              <ul class="package-includes">
                <li>Editing photostrip template</li>
                <li>Pilih dari koleksi template</li>
                <li>Cetak foto online</li>
              </ul>
              <a href="https://wa.me/6281234567890?text=Halo!%20Saya%20ingin%20pesan%20paket%20Template%20Photostrip%20%F0%9F%93%B8" class="btn-wa" target="_blank">Pesan Sekarang</a>
              </div>
            </div>

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
        <a href="https://wa.me/6281234567890?text=Halo!%20Saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20FrameSmile%20%F0%9F%93%B8" class="btn-wa btn-wa-large" target="_blank">Chat Admin Sekarang</a>
      </section>
    </main>

    <?php require_once __DIR__ . '/../components/footer.php'; ?>


</body>

</html>