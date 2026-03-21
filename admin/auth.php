<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['fs_admin'])) {
  header('Location: login.php');
  exit;
}