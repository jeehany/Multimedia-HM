<?php
require_once __DIR__ . '/../../config.php';
require_login();

if(can_edit() && isset($_GET['delete'])){
    $id=(int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tabel_pembelian WHERE id_pembelian = $id");
    header('Location: pembelian.php');
    exit;
}

if(can_approve() && isset($_GET['approve']) && isset($_GET['status_baru'])){
    $id = (int)$_GET['approve'];
    $status_baru = in_array($_GET['status_baru'], ['disetujui', 'ditolak']) ? $_GET['status_baru'] : 'menunggu';
    mysqli_query($conn, "UPDATE tabel_pembelian SET status='$status_baru' WHERE id_pembelian=$id");
    header('Location: pembelian.php');
    exit;
}

include __DIR__ . '/../../header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="fas fa-shopping-cart me-2" style="color: #4f46e5;"></i>Permohonan Pembelian</h2>
    <p class="mb-0">Kelola permohonan pembelian alat multimedia</p>
  </div>
  <?php if(can_edit()): ?>
  <a class="btn btn-primary" href="pembelian_add.php">
    <i class="fas fa-plus me-1"></i> Tambah Permohonan
  </a>
  <?php endif; ?>
</div>

<?php if(!can_edit() && !can_approve()): ?>
<div class="alert alert-info mb-4">
  <i class="fas fa-info-circle me-2"></i>Mode Baca Saja - Hanya Kepala yang dapat menyetujui/menolak
</div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" class="row g-3 auto-filter">
      <div class="col-md-3">
        <label class="form-label small text-muted">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc;"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
          <input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari nama atau alasan...">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Tanggal Awal</label>
        <input type="date" name="tgl_awal" class="form-control" value="<?=htmlspecialchars($_GET['tgl_awal'] ?? '')?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" class="form-control" value="<?=htmlspecialchars($_GET['tgl_akhir'] ?? '')?>">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Status</label>
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="menunggu" <?=(!empty($_GET['status']) && $_GET['status']=='menunggu')?'selected':''?>>Menunggu</option>
          <option value="disetujui" <?=(!empty($_GET['status']) && $_GET['status']=='disetujui')?'selected':''?>>Disetujui</option>
          <option value="ditolak" <?=(!empty($_GET['status']) && $_GET['status']=='ditolak')?'selected':''?>>Ditolak</option>
        </select>
      </div>
      <div class="col-md-2 align-self-end">
        <a href="pembelian.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
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
            <th>Estimasi Biaya</th>
            <th>Tanggal</th>
            <th>Status</th>
            <?php if(can_edit() || can_approve()): ?><th class="text-center">Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
<?php
// build filters
$where = [];
if(!empty($_GET['q'])){
    $qstr = mysqli_real_escape_string($conn, $_GET['q']);
    $where[] = "(nama_alat LIKE '%$qstr%' OR alasan LIKE '%$qstr%')";
}
if(!empty($_GET['tgl_awal']) && !empty($_GET['tgl_akhir'])){
    $tgl_awal = mysqli_real_escape_string($conn, $_GET['tgl_awal']);
    $tgl_akhir = mysqli_real_escape_string($conn, $_GET['tgl_akhir']);
    $where[] = "tanggal_permohonan BETWEEN '$tgl_awal' AND '$tgl_akhir'";
} elseif(!empty($_GET['tgl_awal'])){
    $tgl_awal = mysqli_real_escape_string($conn, $_GET['tgl_awal']);
    $where[] = "tanggal_permohonan >= '$tgl_awal'";
} elseif(!empty($_GET['tgl_akhir'])){
    $tgl_akhir = mysqli_real_escape_string($conn, $_GET['tgl_akhir']);
    $where[] = "tanggal_permohonan <= '$tgl_akhir'";
}
if(!empty($_GET['status'])){
    $where[] = "status='".mysqli_real_escape_string($conn,$_GET['status'])."'";
}
$sql = "SELECT * FROM tabel_pembelian" . (count($where)? ' WHERE '.implode(' AND ',$where): '') . " ORDER BY id_pembelian DESC";
$q=mysqli_query($conn,$sql);
$no = 1;
while($r=mysqli_fetch_assoc($q)){
  $status_badge = '';
  if($r['status'] === 'menunggu') $status_badge = '<span class="badge bg-warning">Menunggu</span>';
  elseif($r['status'] === 'disetujui') $status_badge = '<span class="badge bg-success">Disetujui</span>';
  elseif($r['status'] === 'ditolak') $status_badge = '<span class="badge bg-danger">Ditolak</span>';
  
  echo '<tr>';
  echo '<td>'.$no++.'</td>';
  echo '<td><strong>'.htmlspecialchars($r['nama_alat']).'</strong></td>';
  echo '<td>Rp '.number_format($r['estimasi_biaya'],0,',','.').'</td>';
  echo '<td>'.date('d M Y', strtotime($r['tanggal_permohonan'])).'</td>';
  echo '<td>'.$status_badge.'</td>';
  
  if(can_approve()) {
      echo '<td class="text-center">';
      if($r['status'] === 'menunggu') {
          echo '<a class="btn btn-sm btn-success me-1" href="?approve='.$r['id_pembelian'].'&status_baru=disetujui" onclick="return confirm(\'Setujui permohonan ini?\')" title="Setujui"><i class="fas fa-check"></i></a>';
          echo '<a class="btn btn-sm btn-danger" href="?approve='.$r['id_pembelian'].'&status_baru=ditolak" onclick="return confirm(\'Tolak permohonan ini?\')" title="Tolak"><i class="fas fa-times"></i></a>';
      } else {
          echo '<span class="text-muted">-</span>';
      }
      echo '</td>';
  } elseif(can_edit()) {
      echo '<td class="text-center">';
      echo '<a class="btn btn-sm btn-primary me-1" href="pembelian_edit.php?id='.$r['id_pembelian'].'" title="Edit"><i class="fas fa-edit"></i></a>';
      echo '<a class="btn btn-sm btn-danger" href="?delete='.$r['id_pembelian'].'" onclick="return confirm(\'Hapus data ini?\')" title="Hapus"><i class="fas fa-trash"></i></a>';
      echo '</td>';
  }
  echo '</tr>';
}
if(mysqli_num_rows($q) == 0){
  echo '<tr><td colspan="'.(can_edit() || can_approve()?6:5).'" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data ditemukan</td></tr>';
}
?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
