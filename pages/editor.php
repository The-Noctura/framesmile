<?php
// pages/product.php — Screen 1: Pilih Produk
// Integrasi ke framesmile — pakai $koneksi dari includes/db.php

session_start();
require_once __DIR__ . '/../includes/db.php';

// Ambil produk dari DB
$products = [];
$res = mysqli_query($koneksi, "SELECT id, slug, name, description, badge FROM products WHERE is_active = 1 ORDER BY sort_order ASC");
while ($row = mysqli_fetch_assoc($res)) {
    $products[] = $row;
}

$selectedId = $_SESSION['fs_product_id'] ?? null;

// Tentukan BASE_URL dari root framesmile
$baseUrl = '/framesmile'; // ← sesuaikan jika nama folder beda
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Produk — FrameSmile</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/designer.css">
</head>
<body>

<!-- NAV -->
<nav class="designer-nav">
  <a href="<?= $baseUrl ?>/public/index.php" class="designer-logo">Frame<em>Smile</em></a>
  <div class="nav-steps">
    <span class="step active">① Produk</span>
    <span class="step">② Template</span>
    <span class="step">③ Customize</span>
  </div>
  <a href="<?= $baseUrl ?>/public/index.php" class="btn-designer-sec" style="font-size:12px;">← Beranda</a>
</nav>

<!-- CONTENT -->
<div class="designer-wrap">
  <h1 class="designer-title">Pilih Jenis Produk</h1>
  <p class="designer-sub">Pilih produk yang ingin kamu desain</p>

  <div class="product-grid">
    <?php foreach ($products as $p):
      $selected = ($p['id'] == $selectedId) ? 'selected' : '';
    ?>
    <div class="product-card <?= $selected ?>"
         data-id="<?= $p['id'] ?>"
         data-name="<?= htmlspecialchars($p['name']) ?>"
         onclick="selectProduct(this)">

      <div class="product-thumb">
        <?php if ($p['slug'] === 'photobooth'): ?>
          <div class="pb-mock">
            <div class="pb-strip"><div></div><div></div><div></div><div></div></div>
            <div class="pb-strip" style="opacity:.6"><div></div><div></div><div></div><div></div></div>
          </div>
        <?php elseif ($p['slug'] === 'thanks-card'): ?>
          <div class="tc-mock"><span>THANK YOU</span></div>
        <?php elseif ($p['slug'] === 'keychain'): ?>
          <div class="kc-mock"><span>foto</span></div>
        <?php else: ?>
          <div style="font-size:40px; opacity:.25;">🎁</div>
        <?php endif; ?>
      </div>

      <div class="product-info">
        <?php if ($p['badge']): ?>
          <div class="product-badge"><?= htmlspecialchars($p['badge']) ?></div>
        <?php endif; ?>
        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- BOTTOM BAR -->
<div class="designer-bottom">
  <div class="bottom-info">
    Dipilih: <b id="selectedName">
      <?= $selectedId ? htmlspecialchars($_SESSION['fs_product_name']) : '— belum dipilih' ?>
    </b>
  </div>
  <div class="bottom-btns">
    <a id="btnNext"
       href="<?= $baseUrl ?>/pages/templates.php"
       class="btn-designer-primary <?= !$selectedId ? 'disabled' : '' ?>">
      Pilih Template →
    </a>
  </div>
</div>

<div id="designerToast" class="designer-toast"></div>

<script src="<?= $baseUrl ?>/public/assets/js/designer.js"></script>
<script>
const BASE_URL = '<?= $baseUrl ?>';
const btnNext  = document.getElementById('btnNext');

async function selectProduct(card) {
  document.querySelectorAll('.product-card').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');

  const id   = card.dataset.id;
  const name = card.dataset.name;

  btnNext.classList.add('disabled');

  const res  = await fetch(BASE_URL + '/pages/save_session.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type: 'product', id: parseInt(id), name })
  });
  const data = await res.json();

  if (data.ok) {
    document.getElementById('selectedName').textContent = name;
    btnNext.classList.remove('disabled');
    showToast(name + ' dipilih ✓');
  }
}
</script>
</body>
</html>
