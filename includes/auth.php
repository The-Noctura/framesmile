<?php
// includes/auth.php
declare(strict_types=1);

function requireAuth(): int {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = (int)($_SESSION["user_id"] ?? 0);
    if ($userId === 0) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }
    return $userId;
}

function jsonResponse(array $data, int $code = 200): never {
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}
