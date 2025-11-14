<?php
require_once 'config.php';
require_login();

if(!can_edit() && !can_approve()) {
  // Hanya Kepala atau Admin yang bisa akses (role 'staff' removed)
}

if(can_edit()) {
    if(isset($_GET['delete']) && $_GET['delete']){
        $id = (int)$_GET['delete'];
        mysqli_query($conn, "DELETE FROM tabel_maintenance WHERE id_maintenance = $id");
        header('Location: maintenance.php'); exit;
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_POST['id'] ?? '';
        $id_alat = (int)$_POST['id_alat'];
        $tanggal = $_POST['tanggal'];
        $jenis = mysqli_real_escape_string($conn, $_POST['jenis_maintenance']);
        $teknisi = mysqli_real_escape_string($conn, $_POST['teknisi']);
        $biaya = (float)$_POST['biaya'];
        $status = $_POST['status'];
        if($id){
            mysqli_query($conn, "UPDATE tabel_maintenance SET id_alat=$id_alat, tanggal='$tanggal', jenis_maintenance='$jenis', teknisi='$teknisi', biaya=$biaya, status='$status' WHERE id_maintenance='".(int)$id."'");
        } else {
            mysqli_query($conn, "INSERT INTO tabel_maintenance (id_alat,tanggal,jenis_maintenance,teknisi,biaya,status) VALUES ($id_alat,'$tanggal','$jenis','$teknisi',$biaya,'$status')");
        }
        header('Location: maintenance.php'); exit;
    }
}

include 'header.php';
?>
<h2>Maintenance Alat</h2>

<?php if(can_edit()): ?>
<div class="card mb-3">
  <div class="card-body">
    <form method="post" class="row g-3">
      <input type="hidden" name="id" id="id">
      <div class="col-md-4">
        <label class="form-label">Alat</label>
        <select name="id_alat" id="id_alat" class="form-select">
          <?php $rs = mysqli_query($conn, "SELECT id_alat, nama_alat FROM tabel_alat"); while($r = mysqli_fetch_assoc($rs)){ echo '<option value="'.$r['id_alat'].'">'.htmlspecialchars($r['nama_alat']).'</option>'; } ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Jenis Maintenance</label>
        <input type="text" name="jenis_maintenance" id="jenis_maintenance" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Teknisi</label>
        <input type="text" name="teknisi" id="teknisi" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Biaya</label>
        <input type="number" name="biaya" id="biaya" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" id="status" class="form-select"><option value="belum">belum</option><option value="selesai">selesai</option></select>
      </div>
      <div class="col-12">
        <button class="btn btn-success">Simpan</button>
        <button type="reset" class="btn btn-secondary" onclick="clearForm()">Reset</button>
      </div>
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
      $q = mysqli_query($conn, "SELECT m.*, a.nama_alat FROM tabel_maintenance m JOIN tabel_alat a ON m.id_alat=a.id_alat ORDER BY id_maintenance DESC");
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
              echo '<td><a class="btn btn-sm btn-primary me-1" href="#" onclick="edit('.htmlspecialchars(json_encode($r), ENT_QUOTES).')">Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_maintenance'].'" onclick="return confirm(\'Hapus?\')">Hapus</a></td>';
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

<?php if(can_edit()): ?>
<script>
function edit(d){ if(typeof d === 'string') d=JSON.parse(d);
 document.getElementById('id').value = d.id_maintenance;
 document.getElementById('id_alat').value = d.id_alat;
 document.getElementById('jenis_maintenance').value = d.jenis_maintenance;
 document.getElementById('tanggal').value = d.tanggal;
 document.getElementById('teknisi').value = d.teknisi;
 document.getElementById('biaya').value = d.biaya;
 document.getElementById('status').value = d.status;
}
function clearForm(){ document.getElementById('id').value=''; }
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>