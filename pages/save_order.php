<?php
// pages/save_order.php — simpan order + gambar ke server
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { echo json_encode(['ok' => false]); exit; }

// Pastikan folder ada
$uploadDir = __DIR__ . '/../exports/designs/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Simpan gambar PNG
$imagePath = '';
if (!empty($body['image'])) {
    $imgData  = preg_replace('/^data:image\/\w+;base64,/', '', $body['image']);
    $imgData  = base64_decode($imgData);
    $filename = 'order_' . time() . '_' . rand(1000,9999) . '.png';
    file_put_contents($uploadDir . $filename, $imgData);
    $imagePath = 'exports/designs/' . $filename;
}

// Simpan ke DB
$name         = mysqli_real_escape_string($koneksi, $body['name']          ?? '');
$phone        = mysqli_real_escape_string($koneksi, $body['phone']         ?? '');
$note         = mysqli_real_escape_string($koneksi, $body['note']          ?? '');
$productId    = (int)($body['product_id']   ?? 0);
$templateId   = (int)($body['template_id']  ?? 0);
$productName  = mysqli_real_escape_string($koneksi, $body['product_name']  ?? '');
$templateName = mysqli_real_escape_string($koneksi, $body['template_name'] ?? '');
$imgPathEsc   = mysqli_real_escape_string($koneksi, $imagePath);

// Buat tabel orders kalau belum ada
mysqli_query($koneksi, "
    CREATE TABLE IF NOT EXISTS `orders` (
        `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `customer_name`  VARCHAR(100),
        `customer_phone` VARCHAR(20),
        `note`           TEXT,
        `product_id`     INT UNSIGNED,
        `template_id`    INT UNSIGNED,
        `product_name`   VARCHAR(100),
        `template_name`  VARCHAR(100),
        `image_path`     VARCHAR(255),
        `design_json`    LONGTEXT,
        `status`         ENUM('pending','processing','done') DEFAULT 'pending',
        `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

mysqli_query($koneksi, "
    INSERT INTO orders (customer_name, customer_phone, note, product_id, template_id, product_name, template_name, image_path)
    VALUES ('$name','$phone','$note',$productId,$templateId,'$productName','$templateName','$imgPathEsc')
");

$orderId = mysqli_insert_id($koneksi);
echo json_encode([
    'ok'       => true,
    'order_id' => 'ORD-' . str_pad($orderId, 4, '0', STR_PAD_LEFT)
]);
