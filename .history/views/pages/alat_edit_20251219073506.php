<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: alat.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: alat.php'); exit; }
$q = mysqli_query($conn, "SELECT * FROM tools WHERE id=$id");
if(!$q || mysqli_num_rows($q)==0){ header('Location: alat.php'); exit; }
$row = mysqli_fetch_assoc($q);
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $nama = mysqli_real_escape_string($conn, $_POST['tool_name']);
  $spec = mysqli_real_escape_string($conn, $_POST['specification']);
  $kondisi = $_POST['current_condition'];
  $tgl = $_POST['created_at'];
  mysqli_query($conn, "UPDATE tools SET tool_name='$nama', specification='$spec', current_condition='$kondisi', created_at='$tgl' WHERE id=$id");
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
        <div class="col-md-6"><label class="form-label">Nama Alat</label><input name="tool_name" class="form-control" value="<?=htmlspecialchars($row['tool_name'])?>" required></div>
        <div class="col-md-6"><label class="form-label">Spesifikasi</label><input name="specification" class="form-control" value="<?=htmlspecialchars($row['specification'])?>"></div>
        <div class="col-md-4"><label class="form-label">Kondisi</label><select name="current_condition" class="form-select"><option value="baik" <?=($row['current_condition']=='baik'?'selected':'')?>>baik</option><option value="rusak ringan" <?=($row['current_condition']=='rusak ringan'?'selected':'')?>>rusak ringan</option><option value="rusak berat" <?=($row['current_condition']=='rusak berat'?'selected':'')?>>rusak berat</option></select></div>
        <div class="col-md-4"><label class="form-label">Tanggal Dibuat</label><input type="date" name="created_at" class="form-control" value="<?=$row['created_at']?>"></div>
        <div class="col-12">
          <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
          <a class="btn btn-secondary" href="alat.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
