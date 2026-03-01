<?php
// admin/save_slots.php — simpan slot JSON ke DB
require_once 'auth.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$body  = json_decode(file_get_contents('php://input'), true);
$id    = (int)($body['id']    ?? 0);
$slots = $body['slots'] ?? [];

if (!$id) { echo json_encode(['ok' => false, 'error' => 'ID tidak valid']); exit; }

$slotsJson = mysqli_real_escape_string($koneksi, json_encode($slots));
mysqli_query($koneksi, "UPDATE templates SET slots_json = '$slotsJson' WHERE id = $id");

echo json_encode(['ok' => true]);
