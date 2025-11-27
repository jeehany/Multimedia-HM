<?php
require_once __DIR__ . '/../../config.php';
require_login();

$uploadDir = __DIR__ . '/../../uploads' . DIRECTORY_SEPARATOR;

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

<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="fas fa-photo-video me-2" style="color: #4f46e5;"></i>Manajemen Konten</h2>
    <p class="mb-0">Kelola galeri konten multimedia (foto, video, audio, desain)</p>
  </div>
  <?php if(can_edit()): ?>
  <a class="btn btn-primary" href="konten_add.php">
    <i class="fas fa-plus me-1"></i> Tambah Konten
  </a>
  <?php endif; ?>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" class="row g-3 auto-filter">
      <div class="col-md-5">
        <label class="form-label small text-muted">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc;"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
          <input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari judul atau deskripsi...">
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label small text-muted">Jenis Konten</label>
        <select name="jenis" class="form-select">
          <option value="">Semua Jenis</option>
          <option value="foto" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='foto')?'selected':''?>>Foto</option>
          <option value="video" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='video')?'selected':''?>>Video</option>
          <option value="audio" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='audio')?'selected':''?>>Audio</option>
          <option value="desain" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='desain')?'selected':''?>>Desain</option>
        </select>
      </div>
      <div class="col-md-3 align-self-end">
        <a href="konten.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h6 class="mb-3"><i class="fas fa-images me-2" style="color: #4f46e5;"></i>Galeri Konten</h6>
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
      
      echo '<div class="card gallery-card" style="width:180px">
        <img src="'.$thumb.'" class="gallery-item card-img-top" style="height:140px; object-fit:cover;" title="'.$thumb_title.'">
        <div class="card-body p-3">
          <h6 class="card-title mb-1" style="font-size:0.875rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'.htmlspecialchars($r['judul']).'</h6>
          <p class="mb-2" style="font-size:0.75rem; color:#64748b;">
            <span class="badge bg-primary">'.ucfirst(htmlspecialchars($r['jenis'])).'</span>
          </p>
          <p class="mb-2" style="font-size:0.75rem; color:#64748b;">
            <i class="fas fa-user me-1"></i>'.htmlspecialchars($r['penanggung_jawab']).'
          </p>';
      if(can_edit()) {
        echo '<div class="d-flex gap-1">
          <a class="btn btn-sm btn-primary flex-grow-1" href="konten_edit.php?id='.$r['id_konten'].'" title="Edit"><i class="fas fa-edit"></i></a>
          <a class="btn btn-sm btn-danger flex-grow-1" href="?delete='.$r['id_konten'].'" onclick="return confirm(\'Hapus konten ini?\')" title="Hapus"><i class="fas fa-trash"></i></a>
        </div>';
      }
      echo '</div></div>';
    }
    if(mysqli_num_rows($q) == 0){
      echo '<div class="empty-state w-100"><i class="fas fa-images"></i><p>Tidak ada konten ditemukan</p></div>';
    }
    ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
