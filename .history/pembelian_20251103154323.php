<?php
require_once 'config.php';
require_login();

if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; mysqli_query($conn, "DELETE FROM tabel_pembelian WHERE id_pembelian = $id"); header('Location: pembelian.php'); exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = $_POST['id'] ?? '';
  $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
  $estimasi = (float)$_POST['estimasi_biaya'];
  $tgl = $_POST['tanggal_permohonan'];
  $status = $_POST['status'];
  if($id){
    mysqli_query($conn, "UPDATE tabel_pembelian SET nama_alat='$nama', alasan='$alasan', estimasi_biaya=$estimasi, tanggal_permohonan='$tgl', status='$status' WHERE id_pembelian='".(int)$id."'");
  } else {
    mysqli_query($conn, "INSERT INTO tabel_pembelian (nama_alat,alasan,estimasi_biaya,tanggal_permohonan,status) VALUES ('$nama','$alasan',$estimasi,'$tgl','$status')");
  }
  header('Location: pembelian.php'); exit;
}

include 'header.php';
?>
<h2>Permohonan Pembelian</h2>
<div class="card mb-3"><div class="card-body">
<form method="post" class="row g-3">
  <input type="hidden" name="id" id="id">
  <div class="col-md-4"><label class="form-label">Nama Alat</label><input name="nama_alat" id="nama_alat" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Estimasi Biaya</label><input type="number" name="estimasi_biaya" id="estimasi_biaya" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Tanggal Permohonan</label><input type="date" name="tanggal_permohonan" id="tanggal_permohonan" class="form-control"></div>
  <div class="col-12"><label class="form-label">Alasan</label><textarea name="alasan" id="alasan" class="form-control"></textarea></div>
  <div class="col-md-3"><label class="form-label">Status</label><select name="status" id="status" class="form-select"><option value="menunggu">menunggu</option><option value="disetujui">disetujui</option><option value="ditolak">ditolak</option></select></div>
  <div class="col-12"><button class="btn btn-success">Simpan</button><button type="reset" class="btn btn-secondary" onclick="clearForm()">Reset</button></div>
</form>
</div></div>

<div class="card"><div class="card-body">
<table class="table"><thead><tr><th>#</th><th>Nama</th><th>Estimasi</th><th>Tgl</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php $q=mysqli_query($conn,"SELECT * FROM tabel_pembelian ORDER BY id_pembelian DESC"); while($r=mysqli_fetch_assoc($q)){
  echo '<tr>';
  echo '<td>'.$r['id_pembelian'].'</td>';
  echo '<td>'.htmlspecialchars($r['nama_alat']).'</td>';
  echo '<td>'.number_format($r['estimasi_biaya'],0,',','.').'</td>';
  echo '<td>'.$r['tanggal_permohonan'].'</td>';
  echo '<td>'.$r['status'].'</td>';
  echo '<td><a class="btn btn-sm btn-primary me-1" href="#" onclick="edit('.htmlspecialchars(json_encode($r), ENT_QUOTES).')">Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_pembelian'].'" onclick="return confirm(\'Hapus?\')">Hapus</a></td>';
  echo '</tr>';
}
?></tbody></table>
</div></div>

<script>
function edit(d){ if(typeof d==='string') d=JSON.parse(d); document.getElementById('id').value=d.id_pembelian; document.getElementById('nama_alat').value=d.nama_alat; document.getElementById('estimasi_biaya').value=d.estimasi_biaya; document.getElementById('tanggal_permohonan').value=d.tanggal_permohonan; document.getElementById('alasan').value=d.alasan; document.getElementById('status').value=d.status; }
function clearForm(){ document.getElementById('id').value=''; }
</script>

<?php include 'footer.php'; ?>