<?php
require_once 'config.php';
// compute base path so links work when pages are nested under /views/pages/
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if(preg_match('#/views/pages/#', $script)){
  $base = preg_replace('#/views/pages/.*$#','', $script);
} else {
  $base = dirname($script);
}
$base = rtrim($base, '/');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HM Multimedia - Aplikasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
</head>
<body>
<?php if(is_logged_in()): ?>
<!-- Top navbar for authenticated users -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $base ?>/index.php">HM Multimedia</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item me-2"><a class="nav-link" href="#"><i class="fa fa-user"></i> <?=htmlspecialchars($_SESSION['user']['nama_user'])?></a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-5">
  <div class="row">
    <!-- Sidebar for authenticated users -->
    <nav class="col-md-2 d-none d-md-block bg-dark sidebar p-3 text-white" style="position:fixed; height:100vh; overflow-y:auto;">
      <div class="sidebar-sticky">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/index.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/alat.php"><i class="fa fa-box"></i> Data Alat</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/maintenance.php"><i class="fa fa-tools"></i> Maintenance</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/pembelian.php"><i class="fa fa-cart-plus"></i> Pembelian</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/pengeluaran.php"><i class="fa fa-money-bill"></i> Pengeluaran</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/konten.php"><i class="fa fa-photo-video"></i> Konten</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/laporan.php"><i class="fa fa-file-pdf"></i> Laporan</a></li>
        </ul>
      </div>
    </nav>
    <!-- Main content area for authenticated users -->
    <main class="col-md-10 ms-sm-auto ms-md-auto px-md-4 py-4" style="margin-left:16.666%;">
<?php else: ?>
<!-- Login page: clean layout without navbar/sidebar -->
<div class="container mt-5">
  <div class="row">
    <main class="col-12 px-md-4 py-4">
<?php endif; ?>
