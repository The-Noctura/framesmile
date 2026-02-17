<?php
// api/save-design.php
declare(strict_types=1);
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/db.php";

$userId = requireAuth();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["error" => "Method not allowed"], 405);
}

$body = json_decode(file_get_contents("php://input"), true);

$canvasState = $body["canvas_state"]  ?? null;
$frameId     = (int)($body["frame_id"] ?? 0);
$exportData  = $body["export_image"]  ?? null;  // base64 data URL

if (!$canvasState || !$frameId || !$exportData) {
    jsonResponse(["error" => "Data tidak lengkap."], 400);
}

// Simpan export image base64 ke file PNG
$exportDir = __DIR__ . "/../exports/designs/";
if (!is_dir($exportDir)) mkdir($exportDir, 0755, true);

$base64 = preg_replace("/^data:image\/\w+;base64,/", "", $exportData);
$imgData = base64_decode($base64);

if ($imgData === false) {
    jsonResponse(["error" => "Export image tidak valid."], 400);
}

$exportFilename = bin2hex(random_bytes(8)) . ".png";
$exportPath     = $exportDir . $exportFilename;
file_put_contents($exportPath, $imgData);

$exportPublicPath = "/exports/designs/" . $exportFilename;

// Simpan ke database dengan prepared statement
$pdo = getPDO();
$stmt = $pdo->prepare(
    "INSERT INTO designs (user_id, frame_id, canvas_state, export_image_path)" .
    " VALUES (:user_id, :frame_id, :canvas_state, :export_image_path)"
);

$stmt->execute([
    ":user_id"          => $userId,
    ":frame_id"         => $frameId,
    ":canvas_state"     => json_encode($canvasState),
    ":export_image_path" => $exportPublicPath,
]);

$designId = (int)$pdo->lastInsertId();
jsonResponse(["success" => true, "design_id" => $designId, "export_url" => $exportPublicPath]);

?>