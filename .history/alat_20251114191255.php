<?php
require_once 'config.php';
require_login();

// Only admin dan staff dapat edit/delete
if (!can_edit()) {
    // Kepala hanya bisa lihat (read-only)
} else {
    $action = $_GET['action'] ?? '';
    if ($action === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        mysqli_query($conn, "DELETE FROM tabel_alat WHERE id_alat = $id");
        header('Location: alat.php'); exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
        $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
        $kondisi = $_POST['kondisi'];
        $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
        $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
        $tgl = $_POST['tanggal_pembelian'];
        if ($id) {
            mysqli_query($conn, "UPDATE tabel_alat SET nama_alat='$nama', jenis='$jenis', kondisi='$kondisi', lokasi='$lokasi', penanggung_jawab='$pj', tanggal_pembelian='$tgl' WHERE id_alat='".(int)$id."'");
        } else {
            mysqli_query($conn, "INSERT INTO tabel_alat (nama_alat,jenis,kondisi,lokasi,penanggung_jawab,tanggal_pembelian) VALUES ('$nama','$jenis','$kondisi','$lokasi','$pj','$tgl')");
        }
        header('Location: alat.php'); exit;
    }
}

include 'header.php';
?>
<h2>Data Alat Multimedia</h2>

<?php if(can_edit()): ?>
<div class="card mb-3">
  <div class="card-body">
    <form method="post" class="row g-3">
      <input type="hidden" name="id" id="alat_id">
      <div class="col-md-4">
        <label class="form-label">Nama Alat</label>
        <input type="text" name="nama_alat" id="nama_alat" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Jenis</label>
        <input type="text" name="jenis" id="jenis" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Kondisi</label>
        <select name="kondisi" id="kondisi" class="form-select">
          <option value="baik">baik</option>
          <option value="rusak ringan">rusak ringan</option>
          <option value="rusak berat">rusak berat</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Lokasi</label>
        <input type="text" name="lokasi" id="lokasi" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Penanggung Jawab</label>
        <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Pembelian</label>
        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control">
      </div>
      <div class="col-md-12">
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
    <table class="table table-striped">
      <thead><tr><th>#</th><th>Nama</th><th>Jenis</th><th>Kondisi</th><th>Lokasi</th><th>PJ</th><th>Tgl Beli</th><?php if(can_edit()): ?><th>Aksi</th><?php endif; ?></tr></thead>
      <tbody>
      <?php
      $rs = mysqli_query($conn, "SELECT * FROM tabel_alat ORDER BY id_alat DESC");
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
              echo '<td><a class="btn btn-sm btn-primary me-1" href="#" onclick="editRow('.htmlspecialchars(json_encode($row), ENT_QUOTES).')">Edit</a> <a class="btn btn-sm btn-danger" href="?action=delete&id='.$row['id_alat'].'" onclick="return confirm(\'Hapus?\')">Hapus</a></td>';
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
function editRow(data){
    if(typeof data === 'string') data = JSON.parse(data);
    document.getElementById('alat_id').value = data.id_alat;
    document.getElementById('nama_alat').value = data.nama_alat;
    document.getElementById('jenis').value = data.jenis;
    document.getElementById('kondisi').value = data.kondisi;
    document.getElementById('lokasi').value = data.lokasi;
    document.getElementById('penanggung_jawab').value = data.penanggung_jawab;
    document.getElementById('tanggal_pembelian').value = data.tanggal_pembelian;
}
function clearForm(){
    document.getElementById('alat_id').value = '';
}
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>
