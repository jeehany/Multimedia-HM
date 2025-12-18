<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: alat.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['tool_name']);
  $spec = mysqli_real_escape_string($conn, $_POST['specification']);
  $kondisi = $_POST['current_condition'];
  $tgl = $_POST['created_at'];
  mysqli_query($conn, "INSERT INTO tools (tool_name,specification,current_condition,created_at) VALUES ('$nama','$spec','$kondisi','$tgl')");
  header('Location: alat.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:800px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Alat</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="tool_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Spesifikasi</label><input name="specification" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Kondisi</label><select name="current_condition" class="form-select"><option value="baik">baik</option><option value="rusak ringan">rusak ringan</option><option value="rusak berat">rusak berat</option></select></div>
        <div class="col-md-4"><label class="form-label">Tanggal Dibuat</label><input type="date" name="created_at" class="form-control"></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="alat.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
