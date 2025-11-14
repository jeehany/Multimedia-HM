<?php
require_once 'config.php';
require_login();

if(!can_edit() && !can_approve()) {
  // Hanya Kepala atau Admin yang bisa akses (role 'staff' removed)
}

// admin delete handler
if(can_edit() && isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tabel_maintenance WHERE id_maintenance = $id");
    header('Location: maintenance.php'); exit;
}

include 'header.php';
?>
<h2>Maintenance Alat</h2>

<?php if(can_edit()): ?>
<!-- Admin search & add button -->
<div class="card mb-3">
  <div class="card-body">
    <form method="get" class="row g-2">
      <div class="col-md-4"><input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari alat atau teknisi..."></div>
      <div class="col-md-3"><select name="status" class="form-select"><option value="">--Status--</option><option value="belum" <?=(!empty($_GET['status']) && $_GET['status']=='belum')?'selected':''?>>belum</option><option value="selesai" <?=(!empty($_GET['status']) && $_GET['status']=='selesai')?'selected':''?>>selesai</option></select></div>
      <div class="col-md-3"><select name="id_alat" class="form-select"><option value="">--Alat--</option><?php $ars=mysqli_query($conn,"SELECT id_alat,nama_alat FROM tabel_alat"); while($ar=mysqli_fetch_assoc($ars)){ echo '<option value="'.$ar['id_alat'].'" '.((!empty($_GET['id_alat']) && $_GET['id_alat']==$ar['id_alat'])?'selected':'').'>'.htmlspecialchars($ar['nama_alat']).'</option>'; } ?></select></div>
      <div class="col-md-2 text-end"><a class="btn btn-success" href="maintenance_add.php"><i class="fa fa-plus"></i> Tambah</a></div>
    </form>
  </div>
</div>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-eye"></i> Mode Baca Saja (Read-Only)</div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <table class="table table-bordered table-sm">
      <thead><tr><th>#</th><th>Alat</th><th>Jenis</th><th>Tanggal</th><th>Teknisi</th><th>Biaya</th><th>Status</th><?php if(can_edit() || can_approve()): ?><th>Aksi</th><?php endif; ?></tr></thead>
      <tbody>
      <?php
      // Build filters
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
      while($r = mysqli_fetch_assoc($q)){
          echo '<tr>';
          echo '<td>'.$r['id_maintenance'].'</td>';
          echo '<td>'.htmlspecialchars($r['nama_alat']).'</td>';
          echo '<td>'.htmlspecialchars($r['jenis_maintenance']).'</td>';
          echo '<td>'.$r['tanggal'].'</td>';
          echo '<td>'.htmlspecialchars($r['teknisi']).'</td>';
          echo '<td>'.number_format($r['biaya'],0,',','.').'</td>';
          echo '<td>'.htmlspecialchars($r['status']).'</td>';
          if(can_edit()) {
              echo '<td><a class="btn btn-sm btn-primary me-1" href="maintenance_edit.php?id='.$r['id_maintenance'].'"><i class="fa fa-edit"></i> Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_maintenance'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></td>';
          } elseif(can_approve()) {
              echo '<td><span class="badge bg-secondary">Read-Only</span></td>';
          }
          echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>