<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: maintenance.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id_alat = (int)$_POST['id_alat'];
  $tanggal = $_POST['tanggal'];
  $jenis = mysqli_real_escape_string($conn, $_POST['jenis_maintenance']);
  $teknisi = mysqli_real_escape_string($conn, $_POST['teknisi']);
  $biaya = (float)$_POST['biaya'];
  $status = $_POST['status'];
  mysqli_query($conn, "INSERT INTO tabel_maintenance (id_alat,tanggal,jenis_maintenance,teknisi,biaya,status) VALUES ($id_alat,'$tanggal','$jenis','$teknisi',$biaya,'$status')");
  header('Location: maintenance.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Maintenance</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Alat</label><select name="id_alat" class="form-select"><?php $rs=mysqli_query($conn,"SELECT id_alat,nama_alat FROM tabel_alat"); while($r=mysqli_fetch_assoc($rs)){ echo '<option value="'.$r['id_alat'].'">'.htmlspecialchars($r['nama_alat']).'</option>'; } ?></select></div>
        <div class="col-md-6"><label class="form-label">Jenis Maintenance</label><input name="jenis_maintenance" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Teknisi</label><input name="teknisi" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Biaya</label><input type="number" name="biaya" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="belum">belum</option><option value="selesai">selesai</option></select></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="maintenance.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
