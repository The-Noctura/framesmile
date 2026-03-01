<?php
// pages/editor.php — Screen 3: Slot editor dengan shape clipping dinamis
session_start();
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['fs_product_id'])) {
    header('Location: /framesmile/pages/product.php'); exit;
}

$productId    = (int)$_SESSION['fs_product_id'];
$productName  = $_SESSION['fs_product_name']  ?? '';
$templateId   = (int)($_SESSION['fs_template_id'] ?? 0);
$templateName = $_SESSION['fs_template_name'] ?? 'Custom';
$baseUrl      = '/framesmile';
$adminWA      = '6281234567890'; // ← ganti nomor WA admin

$template = null;
$slots    = [];
if ($templateId) {
    $res      = mysqli_query($koneksi, "SELECT * FROM templates WHERE id=$templateId AND is_active=1");
    $template = mysqli_fetch_assoc($res);
    if ($template && $template['slots_json']) {
        $slots = json_decode($template['slots_json'], true) ?: [];
    }
}

$thumbnailUrl = $template ? $baseUrl . '/' . $template['thumbnail'] : '';
$bgColor      = $template['bg_color'] ?? '#ffffff';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customize — FrameSmile</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>/public/assets/css/designer.css">
  <style>
    body { background: #e8e8e4; font-family: var(--font-body); }

    .editor-layout {
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: calc(100vh - 56px);
    }

    /* ── Left Panel ── */
    .editor-panel {
      background: var(--white);
      border-right: 2px solid var(--black);
      display: flex; flex-direction: column;
      overflow-y: auto;
    }
    .panel-section { padding: 14px; border-bottom: 1px solid var(--light); }
    .panel-title {
      font-family: var(--font-head);
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1.5px;
      color: var(--gray); margin-bottom: 10px;
    }

    /* ── Slot items ── */
    .slot-list { display: flex; flex-direction: column; gap: 7px; }
    .slot-item {
      border: 1.5px solid var(--light); border-radius: var(--radius);
      overflow: hidden; transition: border-color .15s; cursor: pointer;
    }
    .slot-item:hover     { border-color: #ccc; }
    .slot-item.has-photo { border-color: #86efac; }
    .slot-item.active    { border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent-light); }

    .slot-header {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 10px; background: var(--bg);
    }
    .slot-badge {
      width: 20px; height: 20px; border-radius: 50%;
      background: var(--black); color: var(--white);
      font-size: 10px; font-weight: 700; font-family: var(--font-head);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .slot-item.has-photo .slot-badge { background: #16a34a; }
    .slot-item.active    .slot-badge { background: var(--accent); }

    .slot-label  { font-size: 11px; font-weight: 600; flex: 1; }
    .slot-shape  { font-size: 10px; color: var(--gray); }
    .slot-status { font-size: 10px; color: var(--gray); }
    .slot-item.has-photo .slot-status { color: #16a34a; }

    .slot-expand {
      display: none; padding: 8px 10px;
      background: #fafafa; border-top: 1px solid var(--light);
    }
    .slot-item.active .slot-expand { display: block; }
    .slot-thumb-preview {
      width: 100%; height: 72px; object-fit: cover;
      border-radius: 4px; display: block; margin-bottom: 6px;
      border: 1px solid var(--light);
    }
    .slot-actions { display: flex; gap: 5px; }
    .slot-btn {
      flex: 1; padding: 4px 6px; text-align: center;
      border: 1px solid var(--light); border-radius: var(--radius);
      background: var(--white); font-size: 10px; cursor: pointer;
      transition: .12s; font-family: var(--font-body);
    }
    .slot-btn:hover { border-color: var(--accent); color: var(--accent); }
    .slot-btn.danger:hover { border-color: #fca5a5; color: #dc2626; }

    /* ── Progress ── */
    .progress-label {
      display: flex; justify-content: space-between;
      font-size: 11px; margin-bottom: 5px;
    }
    .progress-label b { color: var(--accent); }
    .progress-bar { height: 5px; background: var(--light); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--accent); border-radius: 10px; transition: width .3s; }

    /* ── Tip ── */
    .tip-box {
      background: var(--accent-light); border: 1px solid #ffd0d0;
      border-radius: var(--radius); padding: 10px 12px;
      font-size: 11px; line-height: 1.6; color: #cc4444;
    }

    /* ── Canvas area ── */
    .canvas-area {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      position: relative; overflow: hidden;
      padding: 48px 24px 80px;
    }

    /* The main canvas element */
    #mainCanvas {
      display: block;
      box-shadow: 8px 8px 28px rgba(0,0,0,.22);
      cursor: default;
    }

    /* Zoom */
    .zoom-bar {
      position: absolute; bottom: 14px; right: 14px;
      background: var(--white); border: 1.5px solid var(--black);
      border-radius: var(--radius); display: flex; overflow: hidden;
    }
    .zoom-btn {
      padding: 5px 11px; border: none; border-right: 1px solid var(--light);
      background: transparent; cursor: pointer; font-size: 14px; transition: .12s;
    }
    .zoom-btn:last-child { border-right: none; }
    .zoom-btn:hover { background: var(--bg); }
    .zoom-val {
      padding: 5px 10px; border-right: 1px solid var(--light);
      font-size: 11px; color: var(--gray); min-width: 44px; text-align: center;
      line-height: 1.8;
    }

    /* Bottom bar */
    .editor-bottom {
      position: fixed; bottom: 0; left: 260px; right: 0;
      height: 60px; background: var(--white);
      border-top: 2px solid var(--black); border-left: 2px solid var(--black);
      display: flex; align-items: center;
      justify-content: space-between; padding: 0 20px; z-index: 90;
    }
    .editor-info { font-size: 11px; color: var(--gray); }
    .editor-info b { color: var(--black); font-weight: 600; }

    /* Order modal */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.5); z-index: 300;
      align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
      background: var(--white); border: 2px solid var(--black);
      border-radius: var(--radius); width: 420px; max-width: 92vw;
      box-shadow: 6px 6px 0 var(--black); overflow: hidden;
    }
    .modal-header { padding: 20px 24px 14px; border-bottom: 1px solid var(--light); }
    .modal-title { font-family: var(--font-head); font-size: 18px; font-weight: 800; margin-bottom: 4px; }
    .modal-sub   { font-size: 12px; color: var(--gray); }
    .modal-body  { padding: 18px 24px; }
    .modal-footer { padding: 0 24px 18px; display: flex; gap: 8px; justify-content: flex-end; }
    .form-group  { margin-bottom: 13px; }
    .form-label  { display: block; font-family: var(--font-head); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 5px; }
    .form-input  { width: 100%; padding: 9px 12px; border: 1.5px solid var(--black); border-radius: var(--radius); font-family: var(--font-body); font-size: 13px; transition: border-color .15s; }
    .form-input:focus { outline: none; border-color: var(--accent); }
  </style>
</head>
<body>

<!-- NAV -->
<nav class="designer-nav">
  <a href="<?= $baseUrl ?>/public/index.php" class="designer-logo">Frame<em>Smile</em></a>
  <div class="nav-steps">
    <a href="<?= $baseUrl ?>/pages/product.php"   class="step done">① Produk</a>
    <a href="<?= $baseUrl ?>/pages/templates.php" class="step done">② Template</a>
    <span class="step active">③ Customize</span>
  </div>
  <button class="btn-designer-sec" onclick="openOrderModal()">Pesan Sekarang →</button>
</nav>

<div class="editor-layout">

  <!-- ── LEFT PANEL ── -->
  <div class="editor-panel">

    <!-- Progress -->
    <div class="panel-section">
      <div class="panel-title">Progress</div>
      <div class="progress-label">
        <span>Foto terisi</span>
        <b id="progText">0 / <?= count($slots) ?></b>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" id="progFill" style="width:0%"></div>
      </div>
    </div>

    <!-- Slot list -->
    <?php if (!empty($slots)): ?>
    <div class="panel-section" style="flex:1;">
      <div class="panel-title">Slot Foto</div>
      <div class="slot-list">
        <?php foreach ($slots as $i => $slot):
          $shapeIcon = $slot['type'] === 'circle' ? '◯' : ($slot['type'] === 'poly' ? '⬡' : '▭');
          $shapeName = $slot['type'] === 'circle' ? 'Lingkaran' : ($slot['type'] === 'poly' ? 'Polygon' : 'Kotak');
        ?>
        <div class="slot-item" id="sItem<?= $i ?>" onclick="activateSlot(<?= $i ?>)">
          <div class="slot-header">
            <div class="slot-badge"><?= $i+1 ?></div>
            <div class="slot-label">Slot <?= $i+1 ?></div>
            <div class="slot-shape"><?= $shapeIcon ?></div>
            <div class="slot-status" id="sStat<?= $i ?>">Kosong</div>
          </div>
          <div class="slot-expand" id="sExp<?= $i ?>">
            <img class="slot-thumb-preview" id="sThumb<?= $i ?>" src="" alt=""
                 style="display:none;">
            <div class="slot-actions">
              <div class="slot-btn" onclick="event.stopPropagation();triggerUpload(<?= $i ?>)">🔄 Ganti</div>
              <div class="slot-btn danger" onclick="event.stopPropagation();clearSlot(<?= $i ?>)">🗑 Hapus</div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="panel-section" style="flex:1;display:flex;align-items:center;justify-content:center;">
      <div style="text-align:center;color:var(--gray);font-size:12px;">
        <div style="font-size:36px;margin-bottom:10px;">⚠️</div>
        <p>Template ini belum punya slot.</p>
        <a href="<?= $baseUrl ?>/pages/templates.php" class="btn-designer-sec" style="display:inline-block;margin-top:10px;font-size:11px;">← Ganti Template</a>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($slots)): ?>
    <div class="panel-section">
      <div class="tip-box">
        <b>Cara pakai:</b> Klik slot di kanvas atau daftar kiri → pilih foto dari perangkatmu.
        Foto akan otomatis mengikuti bentuk slot.
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- ── CANVAS AREA ── -->
  <div class="canvas-area" id="canvasArea">
    <?php if ($thumbnailUrl): ?>
      <canvas id="mainCanvas"></canvas>
    <?php else: ?>
      <div style="text-align:center;color:var(--gray);">
        <div style="font-size:48px;">🎨</div>
        <p style="margin-top:10px;font-size:14px;">Tidak ada template dipilih.<br>
        <a href="<?= $baseUrl ?>/pages/templates.php" style="color:var(--accent);">Pilih template dulu</a></p>
      </div>
    <?php endif; ?>

    <div class="zoom-bar">
      <button class="zoom-btn" onclick="zoom(-0.15)">−</button>
      <div class="zoom-val" id="zoomVal">100%</div>
      <button class="zoom-btn" onclick="zoom(+0.15)">+</button>
      <button class="zoom-btn" onclick="zoomFit()" title="Fit">⊡</button>
    </div>
  </div>
</div>

<!-- Hidden file input -->
<input type="file" id="fileInput" accept="image/*" style="display:none" onchange="handleFile(this)">

<!-- Bottom bar -->
<div class="editor-bottom">
  <div class="editor-info">
    <b><?= htmlspecialchars($productName) ?></b> • <?= htmlspecialchars($templateName) ?>
    <span id="fillStatus" style="margin-left:8px;color:var(--accent);font-weight:600;"></span>
  </div>
  <div style="display:flex;gap:8px;">
    <a href="<?= $baseUrl ?>/pages/templates.php" class="btn-designer-sec">← Ganti Template</a>
    <button class="btn-designer-sec" onclick="previewResult()">👁 Preview</button>
    <button class="btn-designer-primary" onclick="openOrderModal()">Pesan Sekarang →</button>
  </div>
</div>

<!-- Order Modal -->
<div class="modal-overlay" id="orderModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Kirim ke Admin</div>
      <div class="modal-sub">Desainmu dikirim via WhatsApp untuk diproses.</div>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input class="form-input" type="text" id="oName" placeholder="Nama kamu">
      </div>
      <div class="form-group">
        <label class="form-label">Nomor WhatsApp</label>
        <input class="form-input" type="tel" id="oPhone" placeholder="08xxxxxxxxxx">
      </div>
      <div class="form-group">
        <label class="form-label">Catatan (opsional)</label>
        <input class="form-input" type="text" id="oNote" placeholder="Ukuran cetak, jumlah, dll">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-designer-sec" onclick="closeOrderModal()">Batal</button>
      <button class="btn-designer-primary" onclick="submitOrder()">Kirim ke WhatsApp →</button>
    </div>
  </div>
</div>

<div id="toast" class="designer-toast"></div>

<script>
// ══════════════════════════════════════
//  CONFIG & STATE
// ══════════════════════════════════════
const SLOTS       = <?= json_encode($slots) ?>;
const TPL_SRC     = '<?= $thumbnailUrl ?>';
const BG_COLOR    = '<?= $bgColor ?>';
const BASE_URL    = '<?= $baseUrl ?>';
const ADMIN_WA    = '<?= $adminWA ?>';
const PRODUCT_ID  = <?= $productId ?>;
const TEMPLATE_ID = <?= $templateId ?>;
const P_NAME      = '<?= htmlspecialchars($productName, ENT_QUOTES) ?>';
const T_NAME      = '<?= htmlspecialchars($templateName, ENT_QUOTES) ?>';

const slotPhotos  = {};   // idx -> { img: HTMLImageElement }
let activeIdx     = null;
let tplImage      = null;
let tplW = 0, tplH = 0;
let scale = 1;

// ══════════════════════════════════════
//  CANVAS SETUP
// ══════════════════════════════════════
const mainCanvas = document.getElementById('mainCanvas');
const ctx2       = mainCanvas ? mainCanvas.getContext('2d') : null;

function loadTemplate() {
  if (!TPL_SRC || !mainCanvas) return;
  tplImage     = new Image();
  tplImage.crossOrigin = 'anonymous';
  tplImage.onload = () => {
    tplW = tplImage.naturalWidth;
    tplH = tplImage.naturalHeight;
    mainCanvas.width  = tplW;
    mainCanvas.height = tplH;
    zoomFit();
    render();
  };
  tplImage.onerror = () => {
    tplImage = new Image();
    tplImage.onload = () => { tplW=tplImage.naturalWidth; tplH=tplImage.naturalHeight; mainCanvas.width=tplW; mainCanvas.height=tplH; zoomFit(); render(); };
    tplImage.src = TPL_SRC;
  };
  tplImage.src = TPL_SRC;
}

// ══════════════════════════════════════
//  RENDER — draw everything to canvas
// ══════════════════════════════════════
function render() {
  if (!ctx2 || !tplW) return;
  ctx2.clearRect(0, 0, tplW, tplH);

  // 1. Background
  ctx2.fillStyle = BG_COLOR;
  ctx2.fillRect(0, 0, tplW, tplH);

  // 2. Draw each slot photo clipped to its shape
  SLOTS.forEach((slot, i) => {
    if (!slotPhotos[i]) {
      drawEmptySlot(slot, i);
      return;
    }
    ctx2.save();
    clipShape(slot);
    drawPhotoInSlot(slotPhotos[i].img, slot);
    ctx2.restore();
  });

  // 3. Template overlay on top (preserves borders/decorations)
  if (tplImage) {
    ctx2.drawImage(tplImage, 0, 0, tplW, tplH);
  }

  // 4. Highlight active slot (on top of everything)
  if (activeIdx !== null && SLOTS[activeIdx]) {
    ctx2.save();
    ctx2.strokeStyle = '#FF7979';
    ctx2.lineWidth   = 3;
    ctx2.setLineDash([6, 4]);
    strokeShape(SLOTS[activeIdx]);
    ctx2.restore();
  }
}

// ── Clip path by shape type ──
function clipShape(slot) {
  ctx2.beginPath();
  if (slot.type === 'rect') {
    ctx2.rect(slot.x, slot.y, slot.w, slot.h);

  } else if (slot.type === 'circle') {
    ctx2.arc(slot.cx, slot.cy, slot.r, 0, Math.PI * 2);

  } else if (slot.type === 'poly') {
    slot.points.forEach((p, j) => j === 0 ? ctx2.moveTo(p.x, p.y) : ctx2.lineTo(p.x, p.y));
    ctx2.closePath();
  }
  ctx2.clip();
}

// ── Stroke shape (for active highlight) ──
function strokeShape(slot) {
  ctx2.beginPath();
  if (slot.type === 'rect') {
    ctx2.rect(slot.x, slot.y, slot.w, slot.h);
  } else if (slot.type === 'circle') {
    ctx2.arc(slot.cx, slot.cy, slot.r, 0, Math.PI * 2);
  } else if (slot.type === 'poly') {
    slot.points.forEach((p,j) => j===0 ? ctx2.moveTo(p.x,p.y) : ctx2.lineTo(p.x,p.y));
    ctx2.closePath();
  }
  ctx2.stroke();
}

// ── Draw photo cover-fit into slot bounding box ──
function drawPhotoInSlot(img, slot) {
  const { bx, by, bw, bh } = slotBounds(slot);
  const scale = Math.max(bw / img.width, bh / img.height);
  const dw    = img.width  * scale;
  const dh    = img.height * scale;
  const dx    = bx + (bw - dw) / 2;
  const dy    = by + (bh - dh) / 2;
  ctx2.drawImage(img, dx, dy, dw, dh);
}

// ── Draw placeholder for empty slot ──
function drawEmptySlot(slot, idx) {
  const { bx, by, bw, bh } = slotBounds(slot);
  const cx = bx + bw / 2, cy = by + bh / 2;

  ctx2.save();
  clipShape(slot);
  ctx2.fillStyle = 'rgba(255,121,121,0.08)';
  ctx2.fill();

  // Dashed border
  ctx2.strokeStyle = 'rgba(255,121,121,0.45)';
  ctx2.lineWidth   = 2;
  ctx2.setLineDash([6, 5]);
  ctx2.stroke();
  ctx2.setLineDash([]);

  // Camera icon + label
  ctx2.fillStyle   = 'rgba(0,0,0,0.25)';
  ctx2.font        = `${Math.min(bw,bh)*0.22}px sans-serif`;
  ctx2.textAlign   = 'center';
  ctx2.textBaseline= 'middle';
  ctx2.fillText('📷', cx, cy - bh * 0.07);

  ctx2.font = `600 ${Math.min(bw,bh)*0.09}px Poppins,sans-serif`;
  ctx2.fillText('KLIK & UPLOAD', cx, cy + bh * 0.12);
  ctx2.restore();
}

// ── Get bounding box for any shape type ──
function slotBounds(slot) {
  if (slot.type === 'rect') {
    return { bx: slot.x, by: slot.y, bw: slot.w, bh: slot.h };
  } else if (slot.type === 'circle') {
    return { bx: slot.cx - slot.r, by: slot.cy - slot.r, bw: slot.r*2, bh: slot.r*2 };
  } else if (slot.type === 'poly') {
    const xs = slot.points.map(p=>p.x), ys = slot.points.map(p=>p.y);
    const minX = Math.min(...xs), minY = Math.min(...ys);
    const maxX = Math.max(...xs), maxY = Math.max(...ys);
    return { bx: minX, by: minY, bw: maxX-minX, bh: maxY-minY };
  }
  return { bx:0, by:0, bw:100, bh:100 };
}

// ══════════════════════════════════════
//  CANVAS CLICK → detect slot hit
// ══════════════════════════════════════
if (mainCanvas) {
  mainCanvas.addEventListener('click', e => {
    if (!tplW) return;
    const rect = mainCanvas.getBoundingClientRect();
    const mx   = (e.clientX - rect.left) / scale;
    const my   = (e.clientY - rect.top)  / scale;

    // Hit-test slots in reverse order (last = top)
    for (let i = SLOTS.length - 1; i >= 0; i--) {
      if (pointInSlot(mx, my, SLOTS[i])) {
        activateSlot(i);
        return;
      }
    }
    // Click outside all slots: deactivate
    deactivateSlot();
  });
}

function pointInSlot(x, y, slot) {
  if (slot.type === 'rect') {
    return x >= slot.x && x <= slot.x+slot.w && y >= slot.y && y <= slot.y+slot.h;
  } else if (slot.type === 'circle') {
    const dx = x - slot.cx, dy = y - slot.cy;
    return dx*dx + dy*dy <= slot.r*slot.r;
  } else if (slot.type === 'poly') {
    return pointInPolygon(x, y, slot.points);
  }
  return false;
}

// Ray-casting polygon hit test
function pointInPolygon(x, y, points) {
  let inside = false;
  for (let i=0, j=points.length-1; i<points.length; j=i++) {
    const xi=points[i].x, yi=points[i].y, xj=points[j].x, yj=points[j].y;
    if (((yi>y)!==(yj>y)) && (x < (xj-xi)*(y-yi)/(yj-yi)+xi)) inside=!inside;
  }
  return inside;
}

// ══════════════════════════════════════
//  SLOT INTERACTION
// ══════════════════════════════════════
function activateSlot(idx) {
  if (activeIdx !== null) {
    document.getElementById('sItem'+activeIdx)?.classList.remove('active');
  }
  activeIdx = idx;
  document.getElementById('sItem'+idx)?.classList.add('active');
  render();

  if (!slotPhotos[idx]) {
    triggerUpload(idx);
  }
}

function deactivateSlot() {
  if (activeIdx !== null) {
    document.getElementById('sItem'+activeIdx)?.classList.remove('active');
  }
  activeIdx = null;
  render();
}

function triggerUpload(idx) {
  activeIdx = idx;
  document.getElementById('fileInput').click();
}

function handleFile(input) {
  if (!input.files[0] || activeIdx === null) return;
  const idx    = activeIdx;
  const reader = new FileReader();
  reader.onload = e => {
    const img = new Image();
    img.onload = () => {
      slotPhotos[idx] = { img };
      updatePanelSlot(idx, e.target.result);
      updateProgress();
      render();
      showToast('Slot ' + (idx+1) + ' terisi ✓');
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
  input.value = '';
}

function clearSlot(idx) {
  delete slotPhotos[idx];
  updatePanelSlot(idx, null);
  updateProgress();
  activeIdx = null;
  render();
}

function updatePanelSlot(idx, dataUrl) {
  const item  = document.getElementById('sItem'+idx);
  const thumb = document.getElementById('sThumb'+idx);
  const stat  = document.getElementById('sStat'+idx);
  if (dataUrl) {
    item?.classList.add('has-photo');
    if (thumb) { thumb.src = dataUrl; thumb.style.display = 'block'; }
    if (stat)  stat.textContent = 'Terisi ✓';
  } else {
    item?.classList.remove('has-photo','active');
    if (thumb) { thumb.src=''; thumb.style.display='none'; }
    if (stat)  stat.textContent = 'Kosong';
  }
}

// ══════════════════════════════════════
//  PROGRESS
// ══════════════════════════════════════
function updateProgress() {
  const total  = SLOTS.length;
  const filled = Object.keys(slotPhotos).length;
  const pct    = total ? Math.round(filled/total*100) : 0;
  document.getElementById('progText').textContent = filled+' / '+total;
  document.getElementById('progFill').style.width = pct+'%';
  const el = document.getElementById('fillStatus');
  el.textContent = total > 0
    ? (filled===total ? '✓ Semua slot terisi!' : filled+' dari '+total+' terisi')
    : '';
}

// ══════════════════════════════════════
//  ZOOM
// ══════════════════════════════════════
function zoom(delta) {
  scale = Math.min(Math.max(scale + delta, 0.15), 3);
  applyZoom();
}
function zoomFit() {
  if (!tplW || !mainCanvas) return;
  const area = document.getElementById('canvasArea');
  const aw   = area.clientWidth  - 80;
  const ah   = area.clientHeight - 120;
  scale      = Math.min(aw/tplW, ah/tplH, 1);
  applyZoom();
}
function applyZoom() {
  if (!mainCanvas) return;
  mainCanvas.style.width  = Math.round(tplW * scale) + 'px';
  mainCanvas.style.height = Math.round(tplH * scale) + 'px';
  document.getElementById('zoomVal').textContent = Math.round(scale*100)+'%';
}

// ══════════════════════════════════════
//  PREVIEW
// ══════════════════════════════════════
function previewResult() {
  const dataUrl = renderFinal();
  const win = window.open('','_blank');
  win.document.write(`
    <html><body style="margin:0;background:#333;display:flex;align-items:center;justify-content:center;min-height:100vh;">
    <img src="${dataUrl}" style="max-width:90vw;max-height:90vh;box-shadow:0 0 30px rgba(0,0,0,.5);">
    <a href="${dataUrl}" download="framesmile-design.png" style="position:fixed;bottom:20px;right:20px;background:#FF7979;color:#fff;padding:10px 20px;border-radius:6px;font-family:sans-serif;text-decoration:none;font-weight:700;">⬇ Download</a>
    </body></html>
  `);
}

// ══════════════════════════════════════
//  RENDER FINAL (full resolution)
// ══════════════════════════════════════
function renderFinal() {
  const offscreen = document.createElement('canvas');
  offscreen.width  = tplW;
  offscreen.height = tplH;
  const oc = offscreen.getContext('2d');

  oc.fillStyle = BG_COLOR;
  oc.fillRect(0, 0, tplW, tplH);

  SLOTS.forEach((slot, i) => {
    if (!slotPhotos[i]) return;
    oc.save();
    // clip
    oc.beginPath();
    if (slot.type==='rect')   { oc.rect(slot.x,slot.y,slot.w,slot.h); }
    else if (slot.type==='circle') { oc.arc(slot.cx,slot.cy,slot.r,0,Math.PI*2); }
    else if (slot.type==='poly') {
      slot.points.forEach((p,j) => j===0?oc.moveTo(p.x,p.y):oc.lineTo(p.x,p.y));
      oc.closePath();
    }
    oc.clip();
    // draw photo
    const img = slotPhotos[i].img;
    const {bx,by,bw,bh} = slotBounds(slot);
    const sc = Math.max(bw/img.width, bh/img.height);
    const dw = img.width*sc, dh = img.height*sc;
    oc.drawImage(img, bx+(bw-dw)/2, by+(bh-dh)/2, dw, dh);
    oc.restore();
  });

  if (tplImage) oc.drawImage(tplImage, 0, 0, tplW, tplH);
  return offscreen.toDataURL('image/png');
}

// ══════════════════════════════════════
//  ORDER
// ══════════════════════════════════════
function openOrderModal() {
  const total  = SLOTS.length;
  const filled = Object.keys(slotPhotos).length;
  if (total > 0 && filled < total) {
    showToast('Isi semua '+total+' slot foto dulu!'); return;
  }
  document.getElementById('orderModal').classList.add('open');
}
function closeOrderModal() { document.getElementById('orderModal').classList.remove('open'); }

async function submitOrder() {
  const name  = document.getElementById('oName').value.trim();
  const phone = document.getElementById('oPhone').value.trim();
  const note  = document.getElementById('oNote').value.trim();
  if (!name || !phone) { showToast('Nama & nomor WA wajib diisi!'); return; }

  const imageData = renderFinal();

  try {
    const res  = await fetch(BASE_URL+'/pages/save_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, phone, note, product_id:PRODUCT_ID, template_id:TEMPLATE_ID, product_name:P_NAME, template_name:T_NAME, image:imageData })
    });
    const data = await res.json();
    if (data.ok) {
      const msg = encodeURIComponent(
        `Halo! Saya ingin order di FrameSmile 😊\n\n`+
        `👤 Nama: ${name}\n`+`📦 Produk: ${P_NAME}\n`+
        `🎨 Template: ${T_NAME}\n`+`📝 Catatan: ${note||'-'}\n\n`+
        `🔗 ID Order: ${data.order_id}\nDesain sudah tersimpan, mohon diproses ya!`
      );
      window.open(`https://wa.me/${ADMIN_WA}?text=${msg}`, '_blank');
      closeOrderModal();
      showToast('Order terkirim ✓');
    }
  } catch(e) { showToast('Gagal kirim order. Coba lagi.'); }
}

// ══════════════════════════════════════
//  TOAST
// ══════════════════════════════════════
function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
}

// ── INIT ──
window.addEventListener('load', () => {
  loadTemplate();
  updateProgress();
});
window.addEventListener('resize', () => { if (tplW) zoomFit(); });
</script>
</body>
</html>
