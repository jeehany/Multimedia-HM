<?php
require_once __DIR__ . '/../../config.php';
require_login();

if(can_edit() && isset($_GET['delete'])){
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM tools WHERE id = $id");
  header('Location: alat.php'); exit;
}

include __DIR__ . '/../../header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="fas fa-box me-2" style="color: #4f46e5;"></i>Data Alat Multimedia</h2>
    <p class="mb-0">Kelola data alat dan inventaris multimedia</p>
  </div>
  <?php if(can_edit()): ?>
  <a class="btn btn-primary" href="alat_add.php">
    <i class="fas fa-plus me-1"></i> Tambah Alat
  </a>
  <?php endif; ?>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" id="alat-filter-form" class="row g-3 auto-filter">
      <div class="col-md-4">
        <label class="form-label small text-muted">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc;"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
          <input type="search" name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari nama atau jenis...">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Kondisi</label>
        <select name="kondisi" class="form-select">
          <option value="">Semua Kondisi</option>
          <option value="baik" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='baik')?'selected':''?>>Baik</option>
          <option value="rusak ringan" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='rusak ringan')?'selected':''?>>Rusak Ringan</option>
          <option value="rusak berat" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='rusak berat')?'selected':''?>>Rusak Berat</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Lokasi</label>
        <input type="text" name="lokasi" value="<?=htmlspecialchars($_GET['lokasi'] ?? '')?>" class="form-control" placeholder="Filter lokasi...">
      </div>
      <div class="col-md-2 align-self-end">
        <a href="alat.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Alat</th>
            <th>Jenis</th>
            <th>Kondisi</th>
            <th>Tgl Dibuat</th>
            <?php if(can_edit()): ?><th class="text-center">Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
<?php
// build filters
$where = [];
if(!empty($_GET['q'])){
      $qstr = mysqli_real_escape_string($conn, $_GET['q']);
      $where[] = "(tool_name LIKE '%$qstr%' OR specification LIKE '%$qstr%')";
}
if(!empty($_GET['kondisi'])){
      $where[] = "current_condition='".mysqli_real_escape_string($conn,$_GET['kondisi'])."'";
}
if(!empty($_GET['lokasi'])){
}
    $sql = "SELECT * FROM tools" . (count($where)? ' WHERE '.implode(' AND ',$where): '') . " ORDER BY id DESC";
$rs = mysqli_query($conn, $sql);
$no = 1;
while($row = mysqli_fetch_assoc($rs)){
  $kondisi = strtolower($row['kondisi']);
  $kondisi_badge = 'bg-success';
  if($kondisi == 'rusak ringan') $kondisi_badge = 'bg-warning';
  elseif($kondisi == 'rusak berat') $kondisi_badge = 'bg-danger';
  
  echo '<tr>';
  echo '<td>'.$no++.'</td>';
  echo '<td><strong>'.htmlspecialchars($row['tool_name']).'</strong></td>';
  echo '<td>'.htmlspecialchars($row['specification']).'</td>';
  echo '<td><span class="badge '.$kondisi_badge.'">'.ucfirst(htmlspecialchars($row['current_condition'])).'</span></td>';
  echo '<td>'.date('d M Y', strtotime($row['created_at'])).'</td>';
  if(can_edit()) {
    echo '<td class="text-center">
      <a class="btn btn-sm btn-primary me-1" href="alat_edit.php?id='.$row['id'].'" title="Edit"><i class="fas fa-edit"></i></a>
      <a class="btn btn-sm btn-danger" href="?delete='.$row['id'].'" onclick="return confirm(\'Hapus data ini?\')" title="Hapus"><i class="fas fa-trash"></i></a>
    </td>';
  }
  echo '</tr>';
}
if(mysqli_num_rows($rs) == 0){
  echo '<tr><td colspan="'.(can_edit()?5:4).'" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data ditemukan</td></tr>';
}
?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
