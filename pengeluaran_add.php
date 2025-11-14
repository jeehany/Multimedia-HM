<?php
require_once 'config.php';
require_login();
if(!can_edit()) { header('Location: pengeluaran.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $jenis = $_POST['jenis_pengeluaran'];
  $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $tanggal = $_POST['tanggal'];
  $nominal = (float)$_POST['nominal'];
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
  mysqli_query($conn, "INSERT INTO tabel_pengeluaran (jenis_pengeluaran,nama_alat,tanggal,nominal,keterangan) VALUES ('$jenis','$nama_alat','$tanggal',$nominal,'$keterangan')");
  header('Location: pengeluaran.php'); exit;
}

include 'header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Pengeluaran</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-3"><label class="form-label">Jenis</label><select name="jenis_pengeluaran" class="form-select"><option value="pembelian">pembelian</option><option value="maintenance">maintenance</option></select></div>
        <div class="col-md-4"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control"></div>
        <div class="col-md-2"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Nominal</label><input type="number" name="nominal" class="form-control"></div>
        <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control"></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="pengeluaran.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
