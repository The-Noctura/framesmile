<?php
// pages/save_session.php
// Simpan pilihan product / template ke $_SESSION
// Pakai konvensi framesmile ($koneksi dari includes/db.php)

session_start();
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
$type = $body['type'] ?? '';

if ($type === 'product') {
    $_SESSION['fs_product_id']   = (int)($body['id']   ?? 0);
    $_SESSION['fs_product_name'] = strip_tags($body['name'] ?? '');
    // Reset template saat ganti produk
    unset($_SESSION['fs_template_id'], $_SESSION['fs_template_name']);
    echo json_encode(['ok' => true]);

} elseif ($type === 'template') {
    $_SESSION['fs_template_id']   = (int)($body['id']   ?? 0);
    $_SESSION['fs_template_name'] = strip_tags($body['name'] ?? '');
    echo json_encode(['ok' => true]);

} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Unknown type']);
}
