<?php
require_once 'config.php';
// compute base path so links work when pages are nested under /views/pages/ or /reports/
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if(preg_match('#/views/pages/#', $script)){
  $base = preg_replace('#/views/pages/.*$#','', $script);
} elseif(preg_match('#/reports/#', $script)){
  $base = preg_replace('#/reports/.*$#','', $script);
} else {
  $base = dirname($script);
}
$base = rtrim($base, '/');

// Get current page for active menu
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HM Multimedia - Sistem Manajemen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="<?= $base ?>/assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
</head>
<body>
<?php if(is_logged_in()): ?>
<!-- Top navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $base ?>/index.php">
      <i class="fas fa-play-circle me-2"></i>HM Multimedia
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="fas fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="fas fa-user-circle me-1"></i>
            <?=htmlspecialchars($_SESSION['user']['nama_user'])?>
            <span class="badge bg-primary ms-1"><?=htmlspecialchars($_SESSION['user']['role'] ?? 'user')?></span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?= $base ?>/logout.php">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid" style="padding-top: 0;">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar" style="position:fixed; top:64px; height:calc(100vh - 64px); overflow-y:auto;">
      <div class="sidebar-sticky pt-3">
        <div class="px-3 mb-3">
          <small class="text-uppercase" style="color: rgba(255,255,255,0.4); font-size: 0.7rem; letter-spacing: 0.1em;">Menu Utama</small>
        </div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'index' ? 'active' : '' ?>" href="<?= $base ?>/index.php">
              <i class="fas fa-th-large"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'alat' ? 'active' : '' ?>" href="<?= $base ?>/views/pages/alat.php">
              <i class="fas fa-box"></i> Data Alat
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'maintenance' ? 'active' : '' ?>" href="<?= $base ?>/views/pages/maintenance.php">
              <i class="fas fa-wrench"></i> Maintenance
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'pembelian' ? 'active' : '' ?>" href="<?= $base ?>/views/pages/pembelian.php">
              <i class="fas fa-shopping-cart"></i> Pembelian
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'pengeluaran' ? 'active' : '' ?>" href="<?= $base ?>/views/pages/pengeluaran.php">
              <i class="fas fa-wallet"></i> Pengeluaran
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'konten' ? 'active' : '' ?>" href="<?= $base ?>/views/pages/konten.php">
              <i class="fas fa-photo-video"></i> Konten
            </a>
          </li>
        </ul>
        <div class="px-3 mt-4 mb-3">
          <small class="text-uppercase" style="color: rgba(255,255,255,0.4); font-size: 0.7rem; letter-spacing: 0.1em;">Laporan</small>
        </div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link <?= $currentPage == 'laporan' ? 'active' : '' ?>" href="<?= $base ?>/reports/laporan.php">
              <i class="fas fa-file-alt"></i> Laporan
            </a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- Main content area -->
    <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="margin-left:16.666%;">
<?php else: ?>
<!-- Login page: clean layout -->
<div class="login-container">
<?php endif; ?>
