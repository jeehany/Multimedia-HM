<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: alat.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: alat.php'); exit; }
$q = mysqli_query($conn, "SELECT * FROM tabel_alat WHERE id_alat=$id");
if(!$q || mysqli_num_rows($q)==0){ header('Location: alat.php'); exit; }
$row = mysqli_fetch_assoc($q);
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
  $kondisi = $_POST['kondisi'];
  $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
  $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
  $tgl = $_POST['tanggal_pembelian'];
  mysqli_query($conn, "UPDATE tabel_alat SET nama_alat='$nama', jenis='$jenis', kondisi='$kondisi', lokasi='$lokasi', penanggung_jawab='$pj', tanggal_pembelian='$tgl' WHERE id_alat=$id");
  header('Location: alat.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-edit"></i> Edit Alat</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control" value="<?=htmlspecialchars($row['nama_alat'])?>" required></div>
        <div class="col-md-6"><label class="form-label">Jenis</label><input name="jenis" class="form-control" value="<?=htmlspecialchars($row['jenis'])?>"></div>
        <div class="col-md-4"><label class="form-label">Kondisi</label><select name="kondisi" class="form-select"><option value="baik" <?=($row['kondisi']=='baik'?'selected':'')?>>baik</option><option value="rusak ringan" <?=($row['kondisi']=='rusak ringan'?'selected':'')?>>rusak ringan</option><option value="rusak berat" <?=($row['kondisi']=='rusak berat'?'selected':'')?>>rusak berat</option></select></div>
        <div class="col-md-4"><label class="form-label">Lokasi</label><input name="lokasi" class="form-control" value="<?=htmlspecialchars($row['lokasi'])?>"></div>
        <div class="col-md-4"><label class="form-label">Penanggung Jawab</label><input name="penanggung_jawab" class="form-control" value="<?=htmlspecialchars($row['penanggung_jawab'])?>"></div>
        <div class="col-md-4"><label class="form-label">Tanggal Pembelian</label><input type="date" name="tanggal_pembelian" class="form-control" value="<?=$row['tanggal_pembelian']?>"></div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
          <a class="btn btn-secondary" href="alat.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
