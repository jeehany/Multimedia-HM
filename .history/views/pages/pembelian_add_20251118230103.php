<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_approve()) { header('Location: pembelian.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
  $estimasi = (float)$_POST['estimasi_biaya'];
  $tgl = $_POST['tanggal_permohonan'];
  mysqli_query($conn, "INSERT INTO tabel_pembelian (nama_alat,alasan,estimasi_biaya,tanggal_permohonan,status) VALUES ('$nama','$alasan',$estimasi,'$tgl','menunggu')");
  header('Location: pembelian.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Permohonan Pembelian</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Estimasi Biaya</label><input type="number" name="estimasi_biaya" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Tanggal Permohonan</label><input type="date" name="tanggal_permohonan" class="form-control"></div>
        <div class="col-12"><label class="form-label">Alasan</label><textarea name="alasan" class="form-control"></textarea></div>
        <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="menunggu">menunggu</option><option value="disetujui">disetujui</option><option value="ditolak">ditolak</option></select></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="pembelian.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
