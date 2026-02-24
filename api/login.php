<?php
require_once __DIR__ . '/../app/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $controller = new AuthController();
    $result = $controller->login($username, $password);
    
    if ($result['success']) {
        session_start();
        $_SESSION['user_id'] = $result['user_id'];
        header('Location: ../pages/home.php');
        exit;
    } else {
        header('Location: ../pages/login.php?error=' . urlencode($result['message']));
        exit;
    }
}
?>
