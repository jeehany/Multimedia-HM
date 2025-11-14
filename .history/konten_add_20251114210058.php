<?php
require_once 'config.php';
require_login();
if(!can_edit()) { header('Location: konten.php'); exit; }

$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$err='';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $jenis = $_POST['jenis'];
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
  $tgl = $_POST['tanggal_upload'];
  $file_path = '';

  if(isset($_FILES['file']) && $_FILES['file']['error']===0){
      $fn = time().'_'.basename($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$fn);
      $file_path = 'uploads/'.$fn;
  }
  
  mysqli_query($conn, "INSERT INTO tabel_konten (judul,jenis,deskripsi,penanggung_jawab,tanggal_upload,file_path) VALUES ('$judul','$jenis','$deskripsi','$pj','$tgl','$file_path')");
  header('Location: konten.php'); exit;
}

include 'header.php';
?>
<div class="container">
  <div class="card mx-auto" style="max-width:700px;margin-top:30px">
    <div class="card-body">
      <h4><i class="fa fa-plus-circle"></i> Tambah Konten</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6"><label class="form-label">Judul / Nama File</label><input name="judul" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Jenis Konten</label><select name="jenis" class="form-select"><option value="foto">foto</option><option value="video">video</option><option value="audio">audio</option><option value="desain">desain</option></select></div>
        <div class="col-md-6"><label class="form-label">Tanggal Upload</label><input type="date" name="tanggal_upload" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Penanggung Jawab</label><input name="penanggung_jawab" class="form-control"></div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control"></textarea></div>
        <div class="col-12"><label class="form-label">File</label><input type="file" name="file" class="form-control"></div>
        <div class="col-12">
          <button class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-secondary" href="konten.php"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
