<?php
// api/save-design.php
declare(strict_types=1);
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/db.php";

$userId = requireAuth();
