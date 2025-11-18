<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_approve() && !can_edit()) { header('Location: pembelian.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: pembelian.php'); exit; }
$q = mysqli_query($conn, "SELECT * FROM tabel_pembelian WHERE id_pembelian=$id");
if(!$q || mysqli_num_rows($q)==0){ header('Location: pembelian.php'); exit; }
$row = mysqli_fetch_assoc($q);
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
  $estimasi = (float)$_POST['estimasi_biaya'];
  $tgl = $_POST['tanggal_permohonan'];
  mysqli_query($conn, "UPDATE tabel_pembelian SET nama_alat='$nama', alasan='$alasan', estimasi_biaya=$estimasi, tanggal_permohonan='$tgl' WHERE id_pembelian=$id");
  header('Location: pembelian.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-edit"></i> Edit Permohonan Pembelian</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="nama_alat" class="form-control" value="<?=htmlspecialchars($row['nama_alat'])?>" required></div>
        <div class="col-md-6"><label class="form-label">Estimasi Biaya</label><input type="number" name="estimasi_biaya" class="form-control" value="<?=htmlspecialchars($row['estimasi_biaya'])?>" required></div>
        <div class="col-md-6"><label class="form-label">Tanggal Permohonan</label><input type="date" name="tanggal_permohonan" class="form-control" value="<?=$row['tanggal_permohonan']?>" required></div>
        <div class="col-12"><label class="form-label">Alasan</label><textarea name="alasan" class="form-control" required><?=htmlspecialchars($row['alasan'])?></textarea></div>
        <div class="col-12">
          <p class="text-muted"><i class="fa fa-info-circle"></i> Status akan diubah oleh Kepala setelah permohonan dikirim.</p>
          <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
          <a class="btn btn-secondary" href="pembelian.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
