<?php
// pages/templates.php — Screen 2: Pilih Template
// Integrasi ke framesmile — pakai $koneksi dari includes/db.php

session_start();
require_once __DIR__ . '/../includes/db.php';

// Redirect kalau belum pilih produk
if (empty($_SESSION['fs_product_id'])) {
    header('Location: /framesmile/pages/product.php');
    exit;
}

$productId     = (int)$_SESSION['fs_product_id'];
$productName   = $_SESSION['fs_product_name'] ?? '';
$selectedTplId = $_SESSION['fs_template_id']  ?? null;
$baseUrl       = '/framesmile'; // ← sesuaikan jika nama folder beda

// Ambil categories
$categories = [];
$res = mysqli_query($koneksi, "SELECT id, slug, name FROM template_categories ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($res)) $categories[] = $row;

// Ambil templates berdasarkan produk
$templates = [];
$res = mysqli_query($koneksi, "
    SELECT t.id, t.name, t.border_style, t.color_tag, t.bg_color, t.thumbnail,
           c.slug AS cat_slug, c.name AS cat_name
    FROM templates t
    LEFT JOIN template_categories c ON c.id = t.category_id
    WHERE t.product_id = $productId AND t.is_active = 1
    ORDER BY t.sort_order ASC
");
while ($row = mysqli_fetch_assoc($res)) $templates[] = $row;

$borderClass = [
    'floral'  => 'bp-floral',
    'minimal' => 'bp-minimal',
    'double'  => 'bp-double',
    'vintage' => 'bp-vintage',
    'round'   => 'bp-round',
    'corner'  => 'bp-corner',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Template — FrameSmile</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/designer.css">
</head>
<body>

<!-- NAV -->
<nav class="designer-nav">
  <a href="<?= $baseUrl ?>/public/index.php" class="designer-logo">Frame<em>Smile</em></a>
  <div class="nav-steps">
    <a href="<?= $baseUrl ?>/pages/product.php" class="step done">① Produk</a>
    <span class="step active">② Template</span>
    <span class="step">③ Customize</span>
  </div>
  <a href="<?= $baseUrl ?>/public/index.php" class="btn-designer-sec" style="font-size:12px;">← Beranda</a>
</nav>

<div class="s2-layout">

  <!-- SIDEBAR -->
  <aside class="s2-sidebar">

    <div class="sidebar-section">
      <span class="sidebar-label">Tema</span>
      <div class="chip-group" id="catGroup">
        <button class="chip active" data-val="all">Semua</button>
        <?php foreach ($categories as $cat): ?>
        <button class="chip" data-val="<?= $cat['slug'] ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="sidebar-section">
      <span class="sidebar-label">Warna</span>
      <div class="chip-group" id="colorGroup">
        <button class="chip active" data-val="all">Semua</button>
        <button class="chip" data-val="putih">Putih</button>
        <button class="chip" data-val="pastel">Pastel</button>
        <button class="chip" data-val="earthy">Earthy</button>
        <button class="chip" data-val="dark">Dark</button>
      </div>
    </div>

    <hr class="sidebar-divider">

    <a href="<?= $baseUrl ?>/pages/editor.php" class="btn-blank-tpl">
      ＋ Buat dari Nol
    </a>

  </aside>

  <!-- MAIN -->
  <main class="s2-main">

    <div class="tpl-toolbar">
      <div class="tpl-count">
        Menampilkan <b id="tplCount"><?= count($templates) ?></b> template
        untuk <span style="color:var(--accent); font-weight:600">
          <?= htmlspecialchars($productName) ?>
        </span>
      </div>
      <select class="sort-select" id="sortSelect">
        <option value="default">Terbaru</option>
        <option value="name">A–Z</option>
      </select>
    </div>

    <div class="tpl-grid" id="tplGrid">

      <?php if (empty($templates)): ?>
        <div style="grid-column:1/-1; text-align:center; padding:60px; color:var(--gray);">
          <div style="font-size:36px;">😶</div>
          <p style="margin-top:10px; font-size:14px;">Belum ada template untuk produk ini.</p>
        </div>
      <?php else: ?>

        <?php foreach ($templates as $t):
          $bpClass  = $borderClass[$t['border_style']] ?? 'bp-minimal';
          $bg       = htmlspecialchars($t['bg_color']);
          $selected = ($t['id'] == $selectedTplId) ? 'selected' : '';
        ?>
        <div class="tpl-card <?= $selected ?>"
             data-id="<?= $t['id'] ?>"
             data-name="<?= htmlspecialchars($t['name']) ?>"
             data-cat="<?= htmlspecialchars($t['cat_slug'] ?? '') ?>"
             data-color="<?= htmlspecialchars($t['color_tag']) ?>"
             onclick="selectTemplate(this)">

          <div class="tpl-check">✓</div>

          <div class="tpl-thumb" style="background:<?= $bg ?>">
            <?php if (!empty($t['thumbnail'])): ?>
              <img src="<?= $baseUrl . '/' . htmlspecialchars($t['thumbnail']) ?>"
                   alt="<?= htmlspecialchars($t['name']) ?>">
            <?php else: ?>
              <div class="bp <?= $bpClass ?>" style="background:<?= $bg ?>"></div>
            <?php endif; ?>
          </div>

          <div class="tpl-info">
            <div class="tpl-name"><?= htmlspecialchars($t['name']) ?></div>
            <div class="tpl-meta">
              <?= htmlspecialchars($t['cat_name'] ?? '—') ?> •
              <?= ucfirst(htmlspecialchars($t['color_tag'])) ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

      <?php endif; ?>

      <!-- Blank card -->
      <div class="tpl-card tpl-blank"
           onclick="window.location='<?= $baseUrl ?>/pages/editor.php'">
        <div class="tpl-thumb"><div class="blank-plus">＋</div></div>
        <div class="tpl-info">
          <div class="tpl-name">Kosong / Custom</div>
          <div class="tpl-meta">Desain sendiri</div>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- BOTTOM BAR -->
<div class="designer-bottom s2">
  <div class="bottom-info">
    Template: <b id="selectedTplName">
      <?= $selectedTplId ? htmlspecialchars($_SESSION['fs_template_name']) : '— belum dipilih' ?>
    </b>
  </div>
  <div class="bottom-btns">
    <a href="<?= $baseUrl ?>/pages/product.php" class="btn-designer-sec">← Kembali</a>
    <a id="btnCustomize"
       href="<?= $baseUrl ?>/pages/customize.php"
       class="btn-designer-primary <?= !$selectedTplId ? 'disabled' : '' ?>">
      Pakai Template Ini →
    </a>
  </div>
</div>

<div id="designerToast" class="designer-toast"></div>

<script src="<?= $baseUrl ?>/public/assets/js/designer.js"></script>
<script>
const BASE_URL     = '<?= $baseUrl ?>';
const btnCustomize = document.getElementById('btnCustomize');

async function selectTemplate(card) {
  document.querySelectorAll('.tpl-card[data-id]').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');
  btnCustomize.classList.add('disabled');

  const id   = card.dataset.id;
  const name = card.dataset.name;

  const res  = await fetch(BASE_URL + '/pages/save_session.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ type: 'template', id: parseInt(id), name })
  });
  const data = await res.json();

  if (data.ok) {
    document.getElementById('selectedTplName').textContent = name;
    btnCustomize.classList.remove('disabled');
    showToast('"' + name + '" dipilih ✓');
  }
}

// Filter chips
function initChips(groupId) {
  const group = document.getElementById(groupId);
  group.querySelectorAll('.chip').forEach(chip => {
    chip.addEventListener('click', function () {
      group.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      applyFilter();
    });
  });
}

function applyFilter() {
  const cat   = document.querySelector('#catGroup .chip.active')?.dataset.val   || 'all';
  const color = document.querySelector('#colorGroup .chip.active')?.dataset.val || 'all';
  let count = 0;
  document.querySelectorAll('.tpl-card[data-id]').forEach(card => {
    const show = (cat   === 'all' || card.dataset.cat   === cat) &&
                 (color === 'all' || card.dataset.color === color);
    card.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('tplCount').textContent = count;
}

// Sort
document.getElementById('sortSelect').addEventListener('change', function () {
  const grid  = document.getElementById('tplGrid');
  const cards = [...grid.querySelectorAll('.tpl-card[data-id]')];
  const blank = grid.querySelector('.tpl-blank');
  if (this.value === 'name') {
    cards.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
  } else {
    cards.sort((a, b) => parseInt(a.dataset.id) - parseInt(b.dataset.id));
  }
  cards.forEach(c => grid.insertBefore(c, blank));
});

initChips('catGroup');
initChips('colorGroup');
</script>
</body>
</html>
