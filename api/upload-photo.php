<?php
// api/upload-photo.php
declare(strict_types=1);
require_once __DIR__ . "/../includes/auth.php";

requireAuth();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["error" => "Method not allowed"], 405);
}

$file   = $_FILES["photo"] ?? null;
$errors = [];

// 1. Validasi keberadaan file
if (!$file || $file["error"] !== UPLOAD_ERR_OK) {
    jsonResponse(["error" => "Upload error: " . ($file["error"] ?? "no file")], 400);
}

// 2. Validasi ukuran (maks 10MB)
const MAX_BYTES = 10 * 1024 * 1024;
if ($file["size"] > MAX_BYTES) {
    jsonResponse(["error" => "File terlalu besar. Maks 10MB."], 400);
}

// 3. Validasi MIME (server-side, bukan dari header client)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file["tmp_name"]);
$allowed  = ["image/jpeg", "image/png", "image/webp"];

if (!in_array($mimeType, $allowed, true)) {
    jsonResponse(["error" => "Format tidak didukung. Gunakan JPG/PNG/WEBP."], 400);
}

// 4. Generate nama file aman dengan UUID
$ext      = match($mimeType) {
    "image/jpeg" => "jpg",
    "image/webp" => "webp",
    default       => "png",
};
$uuid     = bin2hex(random_bytes(16));  // 32-char hex = UUID v4 tanpa dash
$filename = $uuid . "." . $ext;
$destDir  = __DIR__ . "/../uploads/photos/";
$destPath = $destDir . $filename;

// 5. Pastikan direktori ada dan writable
if (!is_dir($destDir)) mkdir($destDir, 0755, true);

// 6. Pindahkan file dari temp location
if (!move_uploaded_file($file["tmp_name"], $destPath)) {
    jsonResponse(["error" => "Gagal menyimpan file."], 500);
}

// 7. Return URL yang bisa diakses Fabric.js
$publicUrl = "/uploads/photos/" . $filename;
jsonResponse(["success" => true, "url" => $publicUrl]);

?>