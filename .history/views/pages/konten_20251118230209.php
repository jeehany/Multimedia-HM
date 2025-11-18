<?php
require_once __DIR__ . '/../../config.php';
require_login();

$uploadDir = __DIR__ . '/../../uploads' . DIRECTORY_SEPARATOR;

// admin delete handler
if(can_edit() && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT file_path FROM tabel_konten WHERE id_konten=$id");
    if($q && mysqli_num_rows($q)){
        $f = mysqli_fetch_assoc($q)['file_path'];
        if($f && file_exists(__DIR__ . '/../../' . $f)) @unlink(__DIR__ . '/../../' . $f);
    }
    mysqli_query($conn, "DELETE FROM tabel_konten WHERE id_konten = $id");
    header('Location: konten.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<h2>Manajemen Konten Multimedia</h2>

<?php if(can_edit()): ?>
<div class="card mb-3"><div class="card-body">
  <form method="get" class="row g-2 auto-filter">
    <div class="col-md-4"><input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari judul atau deskripsi..."></div>
    <div class="col-md-3"><select name="jenis" class="form-select"><option value="">--Jenis--</option><option value="foto" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='foto')?'selected':''?>>foto</option><option value="video" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='video')?'selected':''?>>video</option><option value="audio" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='audio')?'selected':''?>>audio</option><option value="desain" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='desain')?'selected':''?>>desain</option></select></div>
    <div class="col-md-2 align-self-end"><a class="btn btn-success" href="konten_add.php"><i class="fa fa-plus"></i> Tambah</a></div>
  </form>
</div></div>

<div class="card"><div class="card-body">
  <h5>Galeri Konten</h5>
  <div class="d-flex flex-wrap gap-3">
    <?php
    $where = '1=1';
    if(!empty($_GET['q'])){
      $qstr = mysqli_real_escape_string($conn, $_GET['q']);
      $where .= " AND (judul LIKE '%$qstr%' OR deskripsi LIKE '%$qstr%')";
    }
    if(!empty($_GET['jenis'])){
      $where .= " AND jenis='".mysqli_real_escape_string($conn,$_GET['jenis'])."'";
    }
    $q=mysqli_query($conn,"SELECT * FROM tabel_konten WHERE $where ORDER BY id_konten DESC");
    while($r=mysqli_fetch_assoc($q)){
      $file_path = $r['file_path'];
      $file_exists = $file_path && file_exists(__DIR__ . '/../../' . $file_path);
      $extension = $file_exists ? strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) : '';
      $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
      $video_exts = ['mp4', 'webm', 'avi', 'mov', 'flv'];
      $audio_exts = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
      
      // Determine thumbnail
      if($file_exists && in_array($extension, $image_exts)) {
        $thumb = htmlspecialchars($file_path);
        $thumb_title = 'Gambar: ' . htmlspecialchars(basename($file_path));
      } elseif($file_exists && in_array($extension, $video_exts)) {
        $thumb = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgZmlsbD0iIzMzMzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjI0IiBmaWxsPSIjZmZmZmZmIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj48dHNwYW4gZm9udC1mYW1pbHk9IkZvbnRBd2Vzb21lIj7Yp9ihPC90c3Bhbj48L3RleHQ+PC9zdmc+';
        $thumb_title = 'Video: ' . htmlspecialchars(basename($file_path));
      } elseif($file_exists && in_array($extension, $audio_exts)) {
        $thumb = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgZmlsbD0iIzMzMzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjI0IiBmaWxsPSIjZmZmZmZmIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj48dHNwYW4gZm9udC1mYW1pbHk9IkZvbnRBd2Vzb21lIj7Yq9igPC90c3Bhbj48L3RleHQ+PC9zdmc+';
        $thumb_title = 'Audio: ' . htmlspecialchars(basename($file_path));
      } else {
        $thumb = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTUwIiBoZWlnaHQ9IjE1MCIgZmlsbD0iIzY2NjY2NiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjI0IiBmaWxsPSIjZmZmZmZmIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj48dHNwYW4gZm9udC1mYW1pbHk9IkZvbnRBd2Vzb21lIj7Yp9iqPC90c3Bhbj48L3RleHQ+PC9zdmc+';
        $thumb_title = 'File tidak ditemukan';
      }
      
      echo '<div class="card" style="width:160px"><img src="'.$thumb.'" class="gallery-item card-img-top" style="height:160px; object-fit:cover;" title="'.$thumb_title.'"><div class="card-body p-2"><h6 class="card-title" style="font-size:13px">'.htmlspecialchars($r['judul']).'</h6><p style="font-size:11px">'.htmlspecialchars($r['jenis']).'<br>'.htmlspecialchars($r['penanggung_jawab']).'</p>';
      if(can_edit()) {
        echo '<div class="d-flex"><a class="btn btn-sm btn-primary me-1" href="konten_edit.php?id='.$r['id_konten'].'"><i class="fa fa-edit"></i> Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_konten'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></div>';
      }
      echo '</div></div>';
    } ?>
  </div>
</div></div>

<?php include __DIR__ . '/../../footer.php'; ?>
