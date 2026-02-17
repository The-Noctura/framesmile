<?php
// pages/editor.php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/db.php";
requireAuth();

// Ambil daftar frame aktif
$pdo    = getPDO();
$frames = $pdo->query("SELECT id, name, file_path, thumbnail FROM frames WHERE is_active = 1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frame Smile — Editor</title>
    <link rel="stylesheet" href="/assets/css/editor.css">
</head>
<body>

<div class="editor-layout">

    <!-- Panel Kiri: Tools -->
    <aside class="tools-panel">
        <h3>Upload Foto</h3>
        <input type="file" id="photo-upload" accept="image/jpeg,image/png,image/webp">

        <h3>Pilih Frame</h3>
        <div class="frame-grid">
            <?php foreach ($frames as $frame): ?>
            <div class="frame-option" data-frame-url="<?= htmlspecialchars($frame["file_path"]) ?>",
                 data-frame-id="<?= $frame["id"] ?>">
                <img src="<?= htmlspecialchars($frame["thumbnail"]) ?>"
                     alt="<?= htmlspecialchars($frame["name"]) ?>">
                <span><?= htmlspecialchars($frame["name"]) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <h3>Transform</h3>
        <label>Putar: <input type="range" id="rotate-slider" min="-180" max="180" value="0"></label>
        <label>Zoom:  <input type="range" id="zoom-slider"  min="0.1"  max="3"   step="0.05" value="1"></label>

        <input type="hidden" id="active-frame-id" value="0">

        <div class="action-buttons">
            <button id="btn-save" class="btn-primary">💾 Simpan Desain</button>
        </div>
    </aside>

    <!-- Area Canvas -->
    <main class="canvas-area">
        <div class="canvas-wrapper">
            <canvas id="design-canvas"></canvas>
        </div>
    </main>

</div>

<script src="/assets/js/vendor/fabric.min.js"></script>
<script type="module" src="/editor/editor.js"></script>
</body>
</html>