<?php
require_once 'config.php';
require_login();

$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

// admin delete handler
if(can_edit() && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT file_path FROM tabel_konten WHERE id_konten=$id");
    if($q && mysqli_num_rows($q)){
        $f = mysqli_fetch_assoc($q)['file_path'];
        if($f && file_exists($f)) @unlink($f);
    }
    mysqli_query($conn, "DELETE FROM tabel_konten WHERE id_konten = $id");
    header('Location: konten.php'); exit;
}

include 'header.php';
?>
<h2>Manajemen Konten Multimedia</h2>

<?php if(can_edit()): ?>
<div class="card mb-3"><div class="card-body">
  <div><a class="btn btn-success mb-3" href="konten_add.php"><i class="fa fa-plus"></i> Tambah Konten</a></div>
</div></div>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-eye"></i> Mode Baca Saja (Read-Only)</div>
<?php endif; ?>

<div class="card"><div class="card-body">
  <h5>Galeri Konten</h5>
  <div class="d-flex flex-wrap gap-3">
    <?php $q=mysqli_query($conn,"SELECT * FROM tabel_konten ORDER BY id_konten DESC"); while($r=mysqli_fetch_assoc($q)){
      $thumb = $r['file_path'] ? $r['file_path'] : 'https://via.placeholder.com/150';
      echo '<div class="card" style="width:160px"><img src="'.htmlspecialchars($thumb).'" class="gallery-item card-img-top"><div class="card-body p-2"><h6 class="card-title" style="font-size:13px">'.htmlspecialchars($r['judul']).'</h6><p style="font-size:11px">'.htmlspecialchars($r['jenis']).'<br>'.htmlspecialchars($r['penanggung_jawab']).'</p>';
      if(can_edit()) {
        echo '<div class="d-flex"><a class="btn btn-sm btn-primary me-1" href="konten_edit.php?id='.$r['id_konten'].'"><i class="fa fa-edit"></i> Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_konten'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></div>';
      }
      echo '</div></div>';
    } ?>
  </div>
</div></div>

<?php if(can_edit()): ?>
<script>
function edit(d){ if(typeof d==='string') d=JSON.parse(d); document.getElementById('id').value=d.id_konten; document.getElementById('judul').value=d.judul; document.getElementById('jenis').value=d.jenis; document.getElementById('tanggal_upload').value=d.tanggal_upload; document.getElementById('penanggung_jawab').value=d.penanggung_jawab; document.getElementById('deskripsi').value=d.deskripsi; }
function clearForm(){ document.getElementById('id').value=''; }
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>