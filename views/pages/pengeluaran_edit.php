<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: pengeluaran.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: pengeluaran.php'); exit; }
$q = mysqli_query($conn, "SELECT * FROM tabel_pengeluaran WHERE id_pengeluaran=$id");
if(!$q || mysqli_num_rows($q)==0){ header('Location: pengeluaran.php'); exit; }
$row = mysqli_fetch_assoc($q);
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $jenis = $_POST['jenis_pengeluaran'];
  $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $tanggal = $_POST['tanggal'];
  $nominal = (float)$_POST['nominal'];
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
  mysqli_query($conn, "UPDATE tabel_pengeluaran SET jenis_pengeluaran='$jenis', nama_alat='$nama_alat', tanggal='$tanggal', nominal=$nominal, keterangan='$keterangan' WHERE id_pengeluaran=$id");
  header('Location: pengeluaran.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-edit"></i> Edit Pengeluaran</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-3"><label class="form-label">Jenis</label><select name="jenis_pengeluaran" class="form-select"><option value="pembelian" <?=($row['jenis_pengeluaran']=='pembelian'?'selected':'')?>>pembelian</option><option value="maintenance" <?=($row['jenis_pengeluaran']=='maintenance'?'selected':'')?>>maintenance</option></select></div>
        <div class="col-md-4"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control" value="<?=htmlspecialchars($row['nama_alat'])?>"></div>
        <div class="col-md-2"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?=$row['tanggal']?>"></div>
        <div class="col-md-3"><label class="form-label">Nominal</label><input type="number" name="nominal" class="form-control" value="<?=htmlspecialchars($row['nominal'])?>"></div>
        <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" class="form-control" value="<?=htmlspecialchars($row['keterangan'])?>"></div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
          <a class="btn btn-secondary" href="pengeluaran.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
