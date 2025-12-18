<?php
require_once __DIR__ . '/../../config.php';
require_login();

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:700px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-info-circle"></i> Fitur Konten Tidak Tersedia</h4>
      <div class="alert alert-warning">Fitur manajemen konten dihapus karena tidak ada padanan tabel pada skema database terbaru.</div>
      <a class="btn btn-primary" href="index.php"><i class="fa fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
