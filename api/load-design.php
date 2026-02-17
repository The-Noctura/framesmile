<?php
// api/load-design.php
declare(strict_types=1);
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/db.php";

$userId   = requireAuth();
$designId = (int)($_GET["id"] ?? 0);

if ($designId === 0) {
    jsonResponse(["error" => "ID desain tidak valid."], 400);
}

$pdo  = getPDO();
$stmt = $pdo->prepare(
    "SELECT id, frame_id, canvas_state, export_image_path, created_at" .
    " FROM designs WHERE id = :id AND user_id = :user_id LIMIT 1"
);
$stmt->execute([":id" => $designId, ":user_id" => $userId]);
$design = $stmt->fetch();

// Cegah akses desain milik user lain
if (!$design) {
    jsonResponse(["error" => "Desain tidak ditemukan."], 404);
}

$design["canvas_state"] = json_decode($design["canvas_state"], true);
jsonResponse(["success" => true, "design" => $design]);

?>