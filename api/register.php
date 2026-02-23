<?php
require_once __DIR__ . '/../app/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $result = $controller->register($_POST);

    if ($result['success']) {
        header('Location: ../pages/login.php?status=registered');
        exit;
    } else {
        header('Location: ../pages/sign-up.php?error=' . urlencode($result['message']));
        exit;
    }
}