<?php
require_once 'config.php';
require_login();
if(!can_edit()) { header('Location: alat.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
  $kondisi = $_POST['kondisi'];
  $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
  $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
  $tgl = $_POST['tanggal_pembelian'];
  mysqli_query($conn, "INSERT INTO tabel_alat (nama_alat,jenis,kondisi,lokasi,penanggung_jawab,tanggal_pembelian) VALUES ('$nama','$jenis','$kondisi','$lokasi','$pj','$tgl')");
  header('Location: alat.php'); exit;
}

include 'header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Alat</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Jenis</label><input name="jenis" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Kondisi</label><select name="kondisi" class="form-select"><option value="baik">baik</option><option value="rusak ringan">rusak ringan</option><option value="rusak berat">rusak berat</option></select></div>
        <div class="col-md-4"><label class="form-label">Lokasi</label><input name="lokasi" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Penanggung Jawab</label><input name="penanggung_jawab" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Tanggal Pembelian</label><input type="date" name="tanggal_pembelian" class="form-control"></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="alat.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>