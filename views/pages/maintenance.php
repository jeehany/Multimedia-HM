<?php
require_once __DIR__ . '/../../config.php';
require_login();

if(can_edit() && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tabel_maintenance WHERE id_maintenance = $id");
    header('Location: maintenance.php'); exit;
}

include __DIR__ . '/../../header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="fas fa-wrench me-2" style="color: #4f46e5;"></i>Maintenance Alat</h2>
    <p class="mb-0">Kelola jadwal dan riwayat maintenance alat multimedia</p>
  </div>
  <?php if(can_edit()): ?>
  <a class="btn btn-primary" href="maintenance_add.php">
    <i class="fas fa-plus me-1"></i> Tambah Maintenance
  </a>
  <?php endif; ?>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" class="row g-3 auto-filter">
      <div class="col-md-3">
        <label class="form-label small text-muted">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text" style="background: #f8fafc;"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
          <input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari alat atau teknisi...">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Status</label>
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          <option value="belum" <?=(!empty($_GET['status']) && $_GET['status']=='belum')?'selected':''?>>Belum</option>
          <option value="selesai" <?=(!empty($_GET['status']) && $_GET['status']=='selesai')?'selected':''?>>Selesai</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Alat</label>
        <select name="id_alat" class="form-select">
          <option value="">Semua Alat</option>
          <?php $ars=mysqli_query($conn,"SELECT id_alat,nama_alat FROM tabel_alat"); while($ar=mysqli_fetch_assoc($ars)){ echo '<option value="'.$ar['id_alat'].'" '.((!empty($_GET['id_alat']) && $_GET['id_alat']==$ar['id_alat'])?'selected':'').'>'.htmlspecialchars($ar['nama_alat']).'</option>'; } ?>
        </select>
      </div>
      <div class="col-md-2 align-self-end">
        <a href="maintenance.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
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
            <th>Tanggal</th>
            <th>Teknisi</th>
            <th>Biaya</th>
            <th>Status</th>
            <?php if(can_edit() || can_approve()): ?><th class="text-center">Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php
        $where = [];
        if(!empty($_GET['q'])){
            $qstr = mysqli_real_escape_string($conn, $_GET['q']);
            $where[] = "(a.nama_alat LIKE '%$qstr%' OR m.teknisi LIKE '%$qstr%')";
        }
        if(!empty($_GET['status'])){
            $where[] = "m.status='".mysqli_real_escape_string($conn,$_GET['status'])."'";
        }
        if(!empty($_GET['id_alat'])){
            $where[] = "m.id_alat='".((int)$_GET['id_alat'])."'";
        }
        $sql = "SELECT m.*, a.nama_alat FROM tabel_maintenance m JOIN tabel_alat a ON m.id_alat=a.id_alat" . (count($where)? ' WHERE '.implode(' AND ',$where): '') . " ORDER BY id_maintenance DESC";
        $q = mysqli_query($conn, $sql);
        $no = 1;
        while($r = mysqli_fetch_assoc($q)){
            $status_badge = $r['status'] === 'selesai' ? 'bg-success' : 'bg-warning';
            echo '<tr>';
            echo '<td>'.$no++.'</td>';
            echo '<td><strong>'.htmlspecialchars($r['nama_alat']).'</strong></td>';
            echo '<td>'.htmlspecialchars($r['jenis_maintenance']).'</td>';
            echo '<td>'.date('d M Y', strtotime($r['tanggal'])).'</td>';
            echo '<td>'.htmlspecialchars($r['teknisi']).'</td>';
            echo '<td>Rp '.number_format($r['biaya'],0,',','.').'</td>';
            echo '<td><span class="badge '.$status_badge.'">'.ucfirst(htmlspecialchars($r['status'])).'</span></td>';
            if(can_edit()) {
                echo '<td class="text-center">
                  <a class="btn btn-sm btn-primary me-1" href="maintenance_edit.php?id='.$r['id_maintenance'].'" title="Edit"><i class="fas fa-edit"></i></a>
                  <a class="btn btn-sm btn-danger" href="?delete='.$r['id_maintenance'].'" onclick="return confirm(\'Hapus data ini?\')" title="Hapus"><i class="fas fa-trash"></i></a>
                </td>';
            } elseif(can_approve()) {
                echo '<td class="text-center"><span class="text-muted">-</span></td>';
            }
            echo '</tr>';
        }
        if(mysqli_num_rows($q) == 0){
            echo '<tr><td colspan="'.(can_edit() || can_approve()?8:7).'" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data ditemukan</td></tr>';
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
