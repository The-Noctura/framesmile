<?php
// admin/templates.php — Kelola template dengan polygon slot editor
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle  = 'Kelola Template';
$baseUrl    = '/framesmile';
$uploadDir  = __DIR__ . '/../public/assets/product-assets/images/templates/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$success = '';
$error   = '';

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $row   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT thumbnail FROM templates WHERE id=$delId"));
    if ($row && $row['thumbnail']) { $fp = __DIR__ . '/../' . $row['thumbnail']; if (file_exists($fp)) unlink($fp); }
    mysqli_query($koneksi, "DELETE FROM templates WHERE id=$delId");
    $success = 'Template berhasil dihapus.';
}
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    mysqli_query($koneksi, "UPDATE templates SET is_active = !is_active WHERE id=$tid");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload') {
    $name       = trim(mysqli_real_escape_string($koneksi, $_POST['name']       ?? ''));
    $productId  = (int)($_POST['product_id']  ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $colorTag   = mysqli_real_escape_string($koneksi, $_POST['color_tag'] ?? 'putih');
    $bgColor    = mysqli_real_escape_string($koneksi, $_POST['bg_color']  ?? '#ffffff');
    $slotsJson  = $_POST['slots_json'] ?? '[]';
    $slots      = json_decode($slotsJson, true);
    if (!is_array($slots)) $slots = [];
    $slotsEsc   = mysqli_real_escape_string($koneksi, json_encode($slots));
    $catVal     = $categoryId ?: 'NULL';

    if (!$name || !$productId) {
        $error = 'Nama template dan produk wajib diisi!';
    } elseif (empty($_FILES['thumbnail']['name'])) {
        $error = 'File template wajib diupload!';
    } else {
        $file = $_FILES['thumbnail'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png','jpg','jpeg'])) { $error = 'Format harus PNG/JPG!'; }
        elseif ($file['size'] > 5*1024*1024)       { $error = 'Maks 5MB!'; }
        else {
            $filename  = 'tpl_' . time() . '_' . rand(100,999) . '.' . $ext;
            $thumbPath = 'public/assets/product-assets/images/templates/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                mysqli_query($koneksi, "INSERT INTO templates (product_id,category_id,name,thumbnail,slots_json,color_tag,bg_color,border_style) VALUES ($productId,$catVal,'$name','$thumbPath','$slotsEsc','$colorTag','$bgColor','minimal')");
                $success = 'Template "' . htmlspecialchars($name) . '" berhasil diupload!';
            } else { $error = 'Gagal upload. Cek permission folder.'; }
        }
    }
}

$templates  = [];
$res = mysqli_query($koneksi, "SELECT t.*, p.name AS product_name, c.name AS cat_name FROM templates t LEFT JOIN products p ON p.id=t.product_id LEFT JOIN template_categories c ON c.id=t.category_id ORDER BY t.created_at DESC");
while ($row = mysqli_fetch_assoc($res)) $templates[] = $row;

$products = [];
$res = mysqli_query($koneksi, "SELECT id,name FROM products WHERE is_active=1 ORDER BY sort_order");
while ($row = mysqli_fetch_assoc($res)) $products[] = $row;

$categories = [];
$res = mysqli_query($koneksi, "SELECT id,name FROM template_categories ORDER BY name");
while ($row = mysqli_fetch_assoc($res)) $categories[] = $row;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Template — FrameSmile Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
  <style>
    .poly-editor-wrap {
      position: relative; display: inline-block;
      border: 1.5px solid var(--light); border-radius: var(--radius);
      overflow: hidden; cursor: crosshair; user-select: none;
      background: repeating-conic-gradient(#ccc 0% 25%,#fff 0% 50%) 0 0/16px 16px;
      width: 100%;
    }
    .poly-editor-wrap img { display: block; width: 100%; max-height: 300px; object-fit: contain; pointer-events: none; }
    #uploadPolyCanvas, #editPolyCanvas {
      position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: all;
    }
    .poly-toolbar { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px; align-items:center; }
    .poly-mode-btn {
      padding: 5px 12px; border: 1.5px solid var(--light); border-radius: var(--radius);
      background: var(--bg); font-size: 11px; font-family: var(--font-body); cursor: pointer; transition: .12s;
    }
    .poly-mode-btn:hover  { border-color: var(--black); }
    .poly-mode-btn.active { background: var(--black); color: var(--white); border-color: var(--black); }
    .poly-mode-btn.auto   { background: var(--accent); color: var(--white); border-color: var(--accent); }
    .poly-mode-btn.auto:hover { background: var(--accent-hover); }
    .poly-hint {
      font-size: 11px; color: var(--gray); padding: 7px 10px;
      background: var(--bg); border-radius: var(--radius);
      border: 1px solid var(--light); margin-bottom: 8px;
    }
    .slot-pills { display:flex; flex-wrap:wrap; gap:5px; margin-top:8px; }
    .slot-pill {
      display:inline-flex; align-items:center; gap:5px; padding:3px 10px;
      background: var(--accent-light); border:1px solid var(--accent);
      border-radius:20px; font-size:11px; color:var(--accent); font-weight:600;
    }
    .slot-pill-del { cursor:pointer; opacity:.7; transition:opacity .12s; }
    .slot-pill-del:hover { opacity:1; }
    .upload-zone {
      border:2px dashed #ccc; border-radius:var(--radius); padding:20px; text-align:center;
      cursor:pointer; transition:.15s; background:var(--bg);
    }
    .upload-zone:hover { border-color:var(--accent); background:var(--accent-light); }
    form { padding: 0 24px; }
    .form-group { margin-bottom: 14px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .modal-scroll { padding: 0 24px; max-height: 62vh; overflow-y: auto; }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>
<div class="admin-main">
  <?php include 'topbar.php'; ?>
  <div class="admin-content">

    <div class="page-header">
      <div>
        <h1 class="page-title">Kelola Template</h1>
        <p class="page-sub"><?= count($templates) ?> template</p>
      </div>
      <button class="btn-primary" onclick="openUploadModal()">＋ Upload Template</button>
    </div>

    <?php if ($success): ?><div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (empty($templates)): ?>
      <div class="empty-state">
        <div style="font-size:48px;">🎨</div>
        <p style="margin-top:12px;">Belum ada template.</p>
        <button class="btn-primary" style="margin-top:16px;" onclick="openUploadModal()">＋ Upload Template</button>
      </div>
    <?php else: ?>
    <div class="template-admin-grid">
      <?php foreach ($templates as $t):
        $slots = json_decode($t['slots_json'] ?? '[]', true) ?: [];
      ?>
      <div class="tpl-admin-card <?= !$t['is_active'] ? 'inactive' : '' ?>">
        <div class="tpl-admin-thumb">
          <?php if ($t['thumbnail']): ?>
            <img src="<?= $baseUrl . '/' . htmlspecialchars($t['thumbnail']) ?>" alt="">
          <?php else: ?>
            <div style="font-size:32px;color:#ddd;">🖼</div>
          <?php endif; ?>
          <?php if (!$t['is_active']): ?><div class="inactive-overlay">Nonaktif</div><?php endif; ?>
          <?php if (!empty($slots)): ?><div class="slot-count-badge"><?= count($slots) ?> slot</div><?php endif; ?>
        </div>
        <div class="tpl-admin-info">
          <div class="tpl-admin-name"><?= htmlspecialchars($t['name']) ?></div>
          <div class="tpl-admin-meta"><?= htmlspecialchars($t['product_name']??'—') ?> • <?= htmlspecialchars($t['cat_name']??'—') ?> • <?= ucfirst($t['color_tag']) ?></div>
        </div>
        <div class="tpl-admin-actions">
          <button class="tbl-btn tbl-btn-edit"
            onclick='openSlotEditor(<?= $t["id"] ?>,<?= json_encode($t["name"]) ?>,"<?= $baseUrl."/".$t["thumbnail"] ?>",<?= htmlspecialchars($t["slots_json"]??"[]",ENT_QUOTES) ?>)'>
            ✏ Edit Slot
          </button>
          <a href="?toggle=<?= $t['id'] ?>" class="tbl-btn tbl-btn-toggle" onclick="return confirm('Ubah status?')">
            <?= $t['is_active'] ? '⏸ Nonaktif' : '▶ Aktif' ?>
          </a>
          <a href="?delete=<?= $t['id'] ?>" class="tbl-btn tbl-btn-del" onclick="return confirm('Hapus template ini?')">🗑</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ════ MODAL UPLOAD ════ -->
<div class="modal-overlay" id="uploadModal">
  <div class="modal" style="width:560px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;">
    <div class="modal-header">
      <h2 class="modal-title">Upload Template Baru</h2>
      <button class="modal-close" onclick="closeUploadModal()">✕</button>
    </div>
    <form method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;overflow:hidden;">
      <input type="hidden" name="action" value="upload">
      <input type="hidden" name="slots_json" id="uploadSlotsJson" value="[]">
      <div class="modal-scroll">
        <div style="height:14px;"></div>
        <div class="form-group">
          <label class="form-label">Nama Template <span style="color:var(--accent)">*</span></label>
          <input class="form-input" type="text" name="name" placeholder="Contoh: Floral Pink Strip" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Produk <span style="color:var(--accent)">*</span></label>
            <select class="form-input" name="product_id" required>
              <option value="">Pilih...</option>
              <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kategori</label>
            <select class="form-input" name="category_id">
              <option value="">— Pilih —</option>
              <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Warna</label>
            <select class="form-input" name="color_tag">
              <option value="putih">Putih</option><option value="pastel">Pastel</option>
              <option value="earthy">Earthy</option><option value="dark">Dark</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Background</label>
            <input class="form-input" type="color" name="bg_color" value="#ffffff" style="height:42px;padding:4px 8px;">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">File Template (PNG transparan) <span style="color:var(--accent)">*</span></label>
          <div class="upload-zone" id="uploadZone" onclick="document.getElementById('thumbFile').click()">
            <div class="upload-icon">📁</div>
            <div class="upload-text">Klik untuk pilih file</div>
            <div class="upload-sub">PNG dengan area transparan · Maks 5MB</div>
            <img id="thumbPreview" style="display:none;max-width:100%;max-height:110px;margin-top:8px;border-radius:4px;">
          </div>
          <input type="file" id="thumbFile" name="thumbnail" accept=".png,.jpg,.jpeg" style="display:none" onchange="onFileSelected(this)">
        </div>
        <div id="uploadSlotSection" style="display:none;">
          <label class="form-label" style="display:block;margin-bottom:8px;">Define Slot Foto</label>
          <div class="poly-toolbar">
            <button type="button" class="poly-mode-btn active" id="upModeRect"   onclick="setMode('upload','rect')">▭ Kotak</button>
            <button type="button" class="poly-mode-btn"        id="upModePoly"   onclick="setMode('upload','poly')">⬡ Polygon</button>
            <button type="button" class="poly-mode-btn"        id="upModeCircle" onclick="setMode('upload','circle')">◯ Lingkaran</button>
            <button type="button" class="poly-mode-btn auto"   onclick="autoDetect('upload')">✨ Auto-detect</button>
          </div>
          <div class="poly-hint" id="uploadHint">Mode <b>Kotak</b>: Klik & drag untuk buat slot</div>
          <div class="poly-editor-wrap" id="uploadEditorWrap">
            <img id="uploadEditorImg" src="" alt="">
            <canvas id="uploadPolyCanvas"></canvas>
          </div>
          <div style="display:flex;gap:6px;margin-top:8px;align-items:center;">
            <button type="button" class="poly-mode-btn" id="btnFinishUpload" style="display:none;" onclick="finishPoly('upload')">✓ Selesai Polygon</button>
            <button type="button" class="tbl-btn tbl-btn-del" onclick="clearAll('upload')">🗑 Hapus Semua</button>
            <span id="uploadSlotCount" style="font-size:11px;color:var(--gray);">0 slot</span>
          </div>
          <div class="slot-pills" id="uploadSlotPills"></div>
        </div>
        <div style="height:8px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-sec" onclick="closeUploadModal()">Batal</button>
        <button type="submit" class="btn-primary">Upload Template →</button>
      </div>
    </form>
  </div>
</div>

<!-- ════ MODAL EDIT SLOT ════ -->
<div class="modal-overlay" id="slotModal">
  <div class="modal" style="width:560px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;">
    <div class="modal-header">
      <h2 class="modal-title" id="slotModalTitle">Edit Slot</h2>
      <button class="modal-close" onclick="closeSlotModal()">✕</button>
    </div>
    <div class="modal-scroll" style="padding:14px 24px;">
      <div class="poly-toolbar">
        <button type="button" class="poly-mode-btn active" id="editModeRect"   onclick="setMode('edit','rect')">▭ Kotak</button>
        <button type="button" class="poly-mode-btn"        id="editModePoly"   onclick="setMode('edit','poly')">⬡ Polygon</button>
        <button type="button" class="poly-mode-btn"        id="editModeCircle" onclick="setMode('edit','circle')">◯ Lingkaran</button>
        <button type="button" class="poly-mode-btn auto"   onclick="autoDetect('edit')">✨ Auto-detect</button>
      </div>
      <div class="poly-hint" id="editHint">Mode <b>Kotak</b>: Klik & drag untuk buat slot</div>
      <div class="poly-editor-wrap" id="editEditorWrap">
        <img id="editEditorImg" src="" alt="">
        <canvas id="editPolyCanvas"></canvas>
      </div>
      <div style="display:flex;gap:6px;margin-top:8px;align-items:center;">
        <button type="button" class="poly-mode-btn" id="btnFinishEdit" style="display:none;" onclick="finishPoly('edit')">✓ Selesai Polygon</button>
        <button type="button" class="tbl-btn tbl-btn-del" onclick="clearAll('edit')">🗑 Hapus Semua</button>
        <span id="editSlotCount" style="font-size:11px;color:var(--gray);">0 slot</span>
      </div>
      <div class="slot-pills" id="editSlotPills"></div>
    </div>
    <div class="modal-footer">
      <button class="btn-sec" onclick="closeSlotModal()">Batal</button>
      <button class="btn-primary" onclick="saveEditSlots()">Simpan Slot →</button>
    </div>
  </div>
</div>

<div id="toast" style="position:fixed;bottom:20px;right:20px;background:#111;color:#fff;padding:9px 16px;border-radius:6px;font-size:12px;font-family:'Poppins',sans-serif;opacity:0;transform:translateY(6px);transition:.2s;pointer-events:none;z-index:9999;"></div>

<script>
// ══════════════════════════════
//  STATE
// ══════════════════════════════
const uploadSlots = [], editSlots = [];
let editTplId = null;
const ds = {
  upload: { mode:'rect', drawing:false, sx:0,sy:0, poly:[] },
  edit:   { mode:'rect', drawing:false, sx:0,sy:0, poly:[] },
};

const HINTS = {
  rect:   'Mode <b>Kotak</b>: Klik & drag untuk buat slot',
  poly:   'Mode <b>Polygon</b>: Klik titik-titik, double-klik atau tombol "Selesai" untuk tutup',
  circle: 'Mode <b>Lingkaran</b>: Klik pusat, drag untuk radius',
};
const COLORS = ['#FF7979','#4ECDC4','#45B7D1','#96CEB4','#FFEAA7','#DDA0DD'];

function slots(ctx) { return ctx==='upload' ? uploadSlots : editSlots; }
function canvas(ctx) { return document.getElementById(ctx+'PolyCanvas'); }
function img(ctx)    { return document.getElementById(ctx+'EditorImg'); }

// ── MODALS ──
function openUploadModal()  { document.getElementById('uploadModal').classList.add('open'); }
function closeUploadModal() { document.getElementById('uploadModal').classList.remove('open'); uploadSlots.length=0; ds.upload.poly=[]; }
function closeSlotModal()   { document.getElementById('slotModal').classList.remove('open'); editSlots.length=0; ds.edit.poly=[]; }

// ── FILE ──
function onFileSelected(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const prev = document.getElementById('thumbPreview');
    prev.src = e.target.result; prev.style.display = 'block';
    ['upload-icon','upload-text','upload-sub'].forEach(c => {
      const el = document.querySelector('.upload-zone .'+c);
      if (el) el.style.display = 'none';
    });
    document.getElementById('uploadSlotSection').style.display = 'block';
    const i = document.getElementById('uploadEditorImg');
    i.src = e.target.result;
    i.onload = () => initCanvas('upload');
  };
  reader.readAsDataURL(input.files[0]);
}

function openSlotEditor(id, name, imgSrc, slotsJson) {
  editTplId = id;
  editSlots.length = 0;
  try { JSON.parse(slotsJson).forEach(s => editSlots.push(s)); } catch(e) {}
  document.getElementById('slotModalTitle').textContent = 'Edit Slot — ' + name;
  const i = document.getElementById('editEditorImg');
  i.src = imgSrc;
  i.onload = () => { initCanvas('edit'); redraw('edit'); };
  document.getElementById('slotModal').classList.add('open');
}

// ── CANVAS INIT ──
function initCanvas(ctx) {
  const c = canvas(ctx), i = img(ctx);
  if (!c || !i) return;
  c.width = i.clientWidth; c.height = i.clientHeight;
  const nc = c.cloneNode(true);
  c.parentNode.replaceChild(nc, c);
  nc.addEventListener('mousedown', e => mdown(ctx,e));
  nc.addEventListener('mousemove', e => mmove(ctx,e));
  nc.addEventListener('mouseup',   e => mup(ctx,e));
  nc.addEventListener('dblclick',  e => { if (ds[ctx].mode==='poly') finishPoly(ctx); });
  redraw(ctx);
}

function coord(ctx, e) {
  const c = canvas(ctx), i = img(ctx);
  const r = c.getBoundingClientRect();
  const sx = (i.naturalWidth  || c.width)  / c.width;
  const sy = (i.naturalHeight || c.height) / c.height;
  return { x:(e.clientX-r.left)*sx, y:(e.clientY-r.top)*sy, px:e.clientX-r.left, py:e.clientY-r.top };
}

// ── MOUSE ──
function mdown(ctx, e) {
  const d = ds[ctx], co = coord(ctx,e);
  if (d.mode==='poly') { d.poly.push({x:co.x,y:co.y}); redraw(ctx); return; }
  d.drawing=true; d.sx=co.x; d.sy=co.y; d.spx=co.px; d.spy=co.py;
}
function mmove(ctx, e) {
  const d = ds[ctx]; if (!d.drawing) return;
  const co = coord(ctx,e); d.ex=co.x; d.ey=co.y; d.epx=co.px; d.epy=co.py;
  redraw(ctx, true);
}
function mup(ctx, e) {
  const d = ds[ctx]; if (!d.drawing) return;
  d.drawing = false;
  const co = coord(ctx,e);
  const sl = slots(ctx);
  if (d.mode==='rect') {
    const x=Math.min(d.sx,co.x), y=Math.min(d.sy,co.y), w=Math.abs(co.x-d.sx), h=Math.abs(co.y-d.sy);
    if (w>5&&h>5) { sl.push({type:'rect',x:Math.round(x),y:Math.round(y),w:Math.round(w),h:Math.round(h)}); updateUI(ctx); }
  } else if (d.mode==='circle') {
    const dx=co.x-d.sx, dy=co.y-d.sy, r=Math.round(Math.sqrt(dx*dx+dy*dy));
    if (r>5) { sl.push({type:'circle',cx:Math.round(d.sx),cy:Math.round(d.sy),r}); updateUI(ctx); }
  }
  redraw(ctx);
}

// ── DRAW ──
function redraw(ctx, drag=false) {
  const c = canvas(ctx);
  if (!c) return;
  const c2 = c.getContext('2d');
  const i  = img(ctx);
  const sx = c.width  / (i.naturalWidth  || c.width);
  const sy = c.height / (i.naturalHeight || c.height);
  const d  = ds[ctx];
  c2.clearRect(0,0,c.width,c.height);

  // Saved slots
  slots(ctx).forEach((s,idx) => {
    const col = COLORS[idx%COLORS.length];
    c2.fillStyle=col+'33'; c2.strokeStyle=col; c2.lineWidth=2; c2.setLineDash([]);
    drawShape(c2, s, sx, sy);
    // Label
    const cx = centerX(s)*sx, cy = centerY(s)*sy;
    c2.fillStyle=col; c2.font='bold 13px Montserrat,sans-serif';
    c2.textAlign='center'; c2.textBaseline='middle';
    c2.fillText(idx+1, cx, cy);
  });

  // Dragging preview
  if (drag && d.drawing) {
    c2.fillStyle='#FF797933'; c2.strokeStyle='#FF7979'; c2.lineWidth=2; c2.setLineDash([4,4]);
    if (d.mode==='rect') {
      const x=Math.min(d.spx,d.epx), y=Math.min(d.spy,d.epy), w=Math.abs(d.epx-d.spx), h=Math.abs(d.epy-d.spy);
      c2.beginPath(); c2.rect(x,y,w,h); c2.fill(); c2.stroke();
    } else if (d.mode==='circle') {
      const r=Math.sqrt((d.epx-d.spx)**2+(d.epy-d.spy)**2);
      c2.beginPath(); c2.arc(d.spx,d.spy,r,0,Math.PI*2); c2.fill(); c2.stroke();
    }
    c2.setLineDash([]);
  }

  // In-progress polygon
  if (d.mode==='poly' && d.poly.length>0) {
    c2.strokeStyle='#FF7979'; c2.lineWidth=2; c2.setLineDash([4,4]);
    c2.beginPath();
    d.poly.forEach((p,j) => j===0 ? c2.moveTo(p.x*sx,p.y*sy) : c2.lineTo(p.x*sx,p.y*sy));
    c2.stroke(); c2.setLineDash([]);
    d.poly.forEach(p => { c2.fillStyle='#FF7979'; c2.beginPath(); c2.arc(p.x*sx,p.y*sy,4,0,Math.PI*2); c2.fill(); });
  }
}

function drawShape(c2, s, sx, sy) {
  c2.beginPath();
  if (s.type==='rect')   { c2.rect(s.x*sx, s.y*sy, s.w*sx, s.h*sy); }
  else if (s.type==='circle') { c2.arc(s.cx*sx, s.cy*sy, s.r*sx, 0, Math.PI*2); }
  else if (s.type==='poly') {
    s.points.forEach((p,j) => j===0 ? c2.moveTo(p.x*sx,p.y*sy) : c2.lineTo(p.x*sx,p.y*sy));
    c2.closePath();
  }
  c2.fill(); c2.stroke();
}

function centerX(s) { return s.type==='rect'?s.x+s.w/2:s.type==='circle'?s.cx:s.points.reduce((a,p)=>a+p.x,0)/s.points.length; }
function centerY(s) { return s.type==='rect'?s.y+s.h/2:s.type==='circle'?s.cy:s.points.reduce((a,p)=>a+p.y,0)/s.points.length; }

// ── MODE ──
function setMode(ctx, mode) {
  ds[ctx].mode = mode; ds[ctx].poly = [];
  const pfx = ctx==='upload'?'upMode':'editMode';
  document.querySelectorAll('[id^="'+pfx+'"]').forEach(b=>b.classList.remove('active'));
  document.getElementById(pfx+mode.charAt(0).toUpperCase()+mode.slice(1)).classList.add('active');
  document.getElementById(ctx+'Hint').innerHTML = HINTS[mode];
  const btnId = 'btnFinish'+(ctx==='upload'?'Upload':'Edit');
  document.getElementById(btnId).style.display = mode==='poly'?'inline-flex':'none';
  redraw(ctx);
}

// ── POLYGON FINISH ──
function finishPoly(ctx) {
  const d = ds[ctx];
  if (d.poly.length<3) { toast('Minimal 3 titik!'); return; }
  slots(ctx).push({type:'poly', points:[...d.poly]});
  d.poly=[];
  updateUI(ctx); redraw(ctx);
  toast('Slot polygon ditambahkan ✓');
}

// ── AUTO DETECT ──
function autoDetect(ctx) {
  const i = img(ctx);
  if (!i.src||!i.naturalWidth) { toast('Upload gambar dulu!'); return; }
  toast('Mendeteksi area transparan...');
  const oc = document.createElement('canvas');
  oc.width=i.naturalWidth; oc.height=i.naturalHeight;
  const c2 = oc.getContext('2d');
  const ti = new Image();
  ti.crossOrigin='anonymous';
  ti.onload = () => { c2.drawImage(ti,0,0); runDetect(ctx,c2,oc.width,oc.height); };
  ti.onerror = () => { try { c2.drawImage(i,0,0); runDetect(ctx,c2,oc.width,oc.height); } catch(e) { toast('Gagal auto-detect. Gambar manual ya!'); } };
  ti.src = i.src;
}

function runDetect(ctx, c2, W, H) {
  const d = c2.getImageData(0,0,W,H);
  const px = d.data;
  const THRESH=30, MIN_AREA=800;
  const mask=new Uint8Array(W*H), labels=new Int32Array(W*H).fill(-1);
  for(let i=0;i<W*H;i++) mask[i]=px[i*4+3]<THRESH?1:0;

  const regions=[]; let lbl=0;
  for(let y=0;y<H;y++) for(let x=0;x<W;x++) {
    const idx=y*W+x;
    if(!mask[idx]||labels[idx]!==-1) continue;
    const queue=[idx], pixels=[];
    labels[idx]=lbl;
    while(queue.length) {
      const cur=queue.shift(); pixels.push(cur);
      const cx=cur%W, cy=Math.floor(cur/W);
      [[cx-1,cy],[cx+1,cy],[cx,cy-1],[cx,cy+1]].forEach(([nx,ny])=>{
        if(nx<0||nx>=W||ny<0||ny>=H) return;
        const ni=ny*W+nx;
        if(mask[ni]&&labels[ni]===-1){labels[ni]=lbl;queue.push(ni);}
      });
    }
    if(pixels.length>=MIN_AREA) {
      let mx=W,my=H,xx=0,xy=0;
      pixels.forEach(p=>{const px=p%W,py=Math.floor(p/W);if(px<mx)mx=px;if(px>xx)xx=px;if(py<my)my=py;if(py>xy)xy=py;});
      const w=xx-mx, h=xy-my;
      const isCircle=Math.abs(w/h-1)<0.4 && pixels.length>Math.PI*(w/2)*(h/2)*0.55;
      regions.push(isCircle
        ? {type:'circle',cx:Math.round(mx+w/2),cy:Math.round(my+h/2),r:Math.round(Math.min(w,h)/2)}
        : {type:'rect',x:mx,y:my,w,h}
      );
    }
    lbl++;
  }
  const sl=slots(ctx); sl.length=0; regions.forEach(r=>sl.push(r));
  updateUI(ctx); redraw(ctx);
  toast(regions.length+' slot terdeteksi ✓');
}

// ── UI ──
function updateUI(ctx) {
  const sl = slots(ctx);
  document.getElementById(ctx+'SlotCount').textContent = sl.length+' slot';
  document.getElementById(ctx+'SlotPills').innerHTML = sl.map((s,i)=>`
    <div class="slot-pill">
      ${s.type==='rect'?'▭':s.type==='circle'?'◯':'⬡'} Slot ${i+1}
      <span class="slot-pill-del" onclick="removeSlot('${ctx}',${i})">✕</span>
    </div>`).join('');
  if (ctx==='upload') document.getElementById('uploadSlotsJson').value=JSON.stringify(sl);
}

function removeSlot(ctx,idx) { slots(ctx).splice(idx,1); updateUI(ctx); redraw(ctx); }
function clearAll(ctx)       { slots(ctx).length=0; ds[ctx].poly=[]; updateUI(ctx); redraw(ctx); }

// ── SAVE EDIT ──
async function saveEditSlots() {
  const res  = await fetch('save_slots.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:editTplId,slots:editSlots})});
  const data = await res.json();
  if(data.ok){toast('Slot disimpan ✓'); closeSlotModal(); setTimeout(()=>location.reload(),900);}
}

// ── TOAST ──
function toast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.style.opacity='1'; t.style.transform='translateY(0)';
  setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(6px)';},2400);
}
</script>
</body>
</html>
