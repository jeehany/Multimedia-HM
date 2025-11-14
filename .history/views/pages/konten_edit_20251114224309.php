<?php
require_once __DIR__ . '/../../config.php';
require_login();
if(!can_edit()) { header('Location: konten.php'); exit; }

$uploadDir = __DIR__ . '/../../uploads' . DIRECTORY_SEPARATOR;
$id = (int)($_GET['id'] ?? 0);
if(!$id) { header('Location: konten.php'); exit; }
$q = mysqli_query($conn, "SELECT * FROM tabel_konten WHERE id_konten=$id");
if(!$q || mysqli_num_rows($q)==0){ header('Location: konten.php'); exit; }
$row = mysqli_fetch_assoc($q);
$err='';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $jenis = $_POST['jenis'];
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
  $tgl = $_POST['tanggal_upload'];

  $sql = "UPDATE tabel_konten SET judul='$judul', jenis='$jenis', deskripsi='$deskripsi', penanggung_jawab='$pj', tanggal_upload='$tgl'";
  
  if(isset($_FILES['file']) && $_FILES['file']['error']===0){
      $fn = time().'_'.basename($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$fn);
      $sql .= ", file_path='uploads/".$fn."'";
  }
  
  $sql .= " WHERE id_konten=$id";
  mysqli_query($conn, $sql);
  header('Location: konten.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:700px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-edit"></i> Edit Konten</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6"><label class="form-label">Judul / Nama File</label><input name="judul" class="form-control" value="<?=htmlspecialchars($row['judul'])?>" required></div>
        <div class="col-md-6"><label class="form-label">Jenis Konten</label><select name="jenis" class="form-select"><option value="foto" <?=($row['jenis']=='foto'?'selected':'')?>>foto</option><option value="video" <?=($row['jenis']=='video'?'selected':'')?>>video</option><option value="audio" <?=($row['jenis']=='audio'?'selected':'')?>>audio</option><option value="desain" <?=($row['jenis']=='desain'?'selected':'')?>>desain</option></select></div>
        <div class="col-md-6"><label class="form-label">Tanggal Upload</label><input type="date" name="tanggal_upload" class="form-control" value="<?=$row['tanggal_upload']?>"></div>
        <div class="col-md-6"><label class="form-label">Penanggung Jawab</label><input name="penanggung_jawab" class="form-control" value="<?=htmlspecialchars($row['penanggung_jawab'])?>"></div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control"><?=htmlspecialchars($row['deskripsi'])?></textarea></div>
        <div class="col-12"><label class="form-label">File (biarkan kosong jika tidak ingin mengganti)</label><input type="file" name="file" class="form-control"></div>
        <?php if($row['file_path']): ?><div class="col-12"><small class="text-muted">File saat ini: <a href="<?=htmlspecialchars($row['file_path'])?>" target="_blank"><?=htmlspecialchars(basename($row['file_path']))?></a></small></div><?php endif; ?>
        <div class="col-12">
          <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
          <a class="btn btn-secondary" href="konten.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../../footer.php'; ?>
