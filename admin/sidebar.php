<?php
// admin/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
  <div class="sidebar-logo">Frame<em>Smile</em></div>
  <div class="sidebar-label">MENU</div>
  <nav class="sidebar-nav">
    <a href="index.php"     class="nav-item <?= $currentPage==='index.php'     ?'active':'' ?>">
      <span>🏠</span> Dashboard
    </a>
    <a href="templates.php" class="nav-item <?= $currentPage==='templates.php' ?'active':'' ?>">
      <span>🎨</span> Template
    </a>
    <a href="orders.php"    class="nav-item <?= $currentPage==='orders.php'    ?'active':'' ?>">
      <span>📋</span> Orders
    </a>
  </nav>
  <div class="sidebar-bottom">
    <a href="../pages/product.php" class="nav-item" target="_blank">
      <span>👁</span> Lihat Toko
    </a>
    <a href="logout.php" class="nav-item nav-logout">
      <span>🚪</span> Logout
    </a>
  </div>
</aside>
