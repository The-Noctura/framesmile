<?php
// admin/logout.php
session_start();
unset($_SESSION['fs_admin']);
session_destroy();
header('Location: login.php');
exit;
