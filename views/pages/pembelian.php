<?php
require_once __DIR__ . '/../../config.php';
require_login();

// admin delete handler
if(can_edit() && isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tabel_pembelian WHERE id_pembelian = $id");
    header('Location: pembelian.php');
    exit;
}

// Kepala dapat approve permohonan
if(can_approve() && isset($_GET['approve']) && isset($_GET['status_baru'])){
    $id = (int)$_GET['approve'];
    $status_baru = in_array($_GET['status_baru'], ['disetujui', 'ditolak']) ? $_GET['status_baru'] : 'menunggu';
    mysqli_query($conn, "UPDATE tabel_pembelian SET status='$status_baru' WHERE id_pembelian=$id");
    header('Location: pembelian.php');
    exit;
}

include __DIR__ . '/../../header.php';
?>
<h2>Permohonan Pembelian</h2>

<?php if(can_edit()): ?>
<div class="card mb-3"><div class="card-body">
  <form method="get" class="row g-2 auto-filter">
    <div class="col-md-4"><input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari nama atau alasan..."></div>
    <div class="col-md-3"><select name="status" class="form-select"><option value="">--Status--</option><option value="menunggu" <?=(!empty($_GET['status']) && $_GET['status']=='menunggu')?'selected':''?>>menunggu</option><option value="disetujui" <?=(!empty($_GET['status']) && $_GET['status']=='disetujui')?'selected':''?>>disetujui</option><option value="ditolak" <?=(!empty($_GET['status']) && $_GET['status']=='ditolak')?'selected':''?>>ditolak</option></select></div>
    <div class="col-md-2 text-end"><a class="btn btn-success" href="pembelian_add.php"><i class="fa fa-plus"></i> Tambah</a></div>
  </form>
</div></div>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-eye"></i> Mode Baca Saja (Read-Only) - Gunakan tombol Approve/Tolak untuk mengubah status</div>
<?php endif; ?>

<div class="card"><div class="card-body">
<table class="table table-sm"><thead><tr><th>#</th><th>Nama</th><th>Estimasi</th><th>Tgl</th><th>Status</th><?php if(can_edit() || can_approve()): ?><th>Aksi</th><?php endif; ?></tr></thead><tbody>
<?php
// build filters
$where = [];
if(!empty($_GET['q'])){
    $qstr = mysqli_real_escape_string($conn, $_GET['q']);
    $where[] = "(nama_alat LIKE '%$qstr%' OR alasan LIKE '%$qstr%')";
}
if(!empty($_GET['status'])){
    $where[] = "status='".mysqli_real_escape_string($conn,$_GET['status'])."'";
}
$sql = "SELECT * FROM tabel_pembelian" . (count($where)? ' WHERE '.implode(' AND ',$where): '') . " ORDER BY id_pembelian DESC";
$q=mysqli_query($conn,$sql);
while($r=mysqli_fetch_assoc($q)){
  $status_badge = '';
  if($r['status'] === 'menunggu') $status_badge = '<span class="badge bg-warning">menunggu</span>';
  elseif($r['status'] === 'disetujui') $status_badge = '<span class="badge bg-success">disetujui</span>';
  elseif($r['status'] === 'ditolak') $status_badge = '<span class="badge bg-danger">ditolak</span>';
  
  echo '<tr>';
  echo '<td>'.$r['id_pembelian'].'</td>';
  echo '<td>'.htmlspecialchars($r['nama_alat']).'</td>';
  echo '<td>'.number_format($r['estimasi_biaya'],0,',','.').'</td>';
  echo '<td>'.$r['tanggal_permohonan'].'</td>';
  echo '<td>'.$status_badge.'</td>';
  
  if(can_edit()) {
      echo '<td><a class="btn btn-sm btn-primary me-1" href="pembelian_edit.php?id='.$r['id_pembelian'].'"><i class="fa fa-edit"></i> Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_pembelian'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></td>';
  } elseif(can_approve()) {
      echo '<td>';
      if($r['status'] === 'menunggu') {
          echo '<a class="btn btn-sm btn-success me-1" href="?approve='.$r['id_pembelian'].'&status_baru=disetujui" onclick="return confirm(\'Setujui permohonan ini?\')">Setujui</a>';
          echo '<a class="btn btn-sm btn-danger" href="?approve='.$r['id_pembelian'].'&status_baru=ditolak" onclick="return confirm(\'Tolak permohonan ini?\')">Tolak</a>';
      } else {
          echo '<span class="badge bg-secondary">'.ucfirst($r['status']).'</span>';
      }
      echo '</td>';
  }
  echo '</tr>';
}
?></tbody></table>
</div></div>

<?php include __DIR__ . '/../../footer.php'; ?>
