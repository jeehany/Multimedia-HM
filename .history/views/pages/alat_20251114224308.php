<?php
require_once __DIR__ . '/../../config.php';
require_login();

// Only admin dapat edit/delete (role 'staff' removed)
// delete handler (admin only)
if(can_edit() && isset($_GET['delete'])){
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM tabel_alat WHERE id_alat = $id");
  header('Location: alat.php'); exit;
}

include __DIR__ . '/../../header.php';
?>
<h2>Data Alat Multimedia</h2>

<div class="card mb-3">
  <div class="card-body">
    <form method="get" id="alat-filter-form" class="row g-2 auto-filter">
      <div class="col-md-4"><input type="search" name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari nama atau jenis..."></div>
      <div class="col-md-2"><select name="kondisi" class="form-select"><option value="">--Kondisi--</option><option value="baik" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='baik')?'selected':''?>>baik</option><option value="rusak ringan" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='rusak ringan')?'selected':''?>>rusak ringan</option><option value="rusak berat" <?=(!empty($_GET['kondisi']) && $_GET['kondisi']=='rusak berat')?'selected':''?>>rusak berat</option></select></div>
      <div class="col-md-3"><input type="text" name="lokasi" value="<?=htmlspecialchars($_GET['lokasi'] ?? '')?>" class="form-control" placeholder="Lokasi"></div>
      <div class="col-md-3 text-end"><?php if(can_edit()): ?><a class="btn btn-success" href="alat_add.php"><i class="fa fa-plus"></i> Tambah Alat</a><?php endif; ?></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-striped">
      <thead><tr><th>#</th><th>Nama</th><th>Jenis</th><th>Kondisi</th><th>Lokasi</th><th>PJ</th><th>Tgl Beli</th><?php if(can_edit()): ?><th>Aksi</th><?php endif; ?></tr></thead>
      <tbody>
<?php
// build filters
$where = [];
if(!empty($_GET['q'])){
  $qstr = mysqli_real_escape_string($conn, $_GET['q']);
  $where[] = "(nama_alat LIKE '%$qstr%' OR jenis LIKE '%$qstr%')";
}
if(!empty($_GET['kondisi'])){
  $where[] = "kondisi='".mysqli_real_escape_string($conn,$_GET['kondisi'])."'";
}
if(!empty($_GET['lokasi'])){
  $where[] = "lokasi LIKE '%".mysqli_real_escape_string($conn,$_GET['lokasi'])."%'";
}
$sql = "SELECT * FROM tabel_alat" . (count($where)? ' WHERE '.implode(' AND ',$where): '') . " ORDER BY id_alat DESC";
$rs = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($rs)){
  echo '<tr>';
  echo '<td>'.$row['id_alat'].'</td>';
  echo '<td>'.htmlspecialchars($row['nama_alat']).'</td>';
  echo '<td>'.htmlspecialchars($row['jenis']).'</td>';
  echo '<td>'.htmlspecialchars($row['kondisi']).'</td>';
  echo '<td>'.htmlspecialchars($row['lokasi']).'</td>';
  echo '<td>'.htmlspecialchars($row['penanggung_jawab']).'</td>';
  echo '<td>'.$row['tanggal_pembelian'].'</td>';
  if(can_edit()) {
    echo '<td><a class="btn btn-sm btn-primary me-1" href="alat_edit.php?id='.$row['id_alat'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn btn-sm btn-danger" href="?delete='.$row['id_alat'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></td>';
  }
  echo '</tr>';
}
?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
