<?php
require_once 'config.php';
require_login();

if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; mysqli_query($conn, "DELETE FROM tabel_pengeluaran WHERE id_pengeluaran = $id"); header('Location: pengeluaran.php'); exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = $_POST['id'] ?? '';
  $jenis = $_POST['jenis_pengeluaran'];
  $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
  $tanggal = $_POST['tanggal'];
  $nominal = (float)$_POST['nominal'];
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
  if($id){
    mysqli_query($conn, "UPDATE tabel_pengeluaran SET jenis_pengeluaran='$jenis', nama_alat='$nama_alat', tanggal='$tanggal', nominal=$nominal, keterangan='$keterangan' WHERE id_pengeluaran='".(int)$id."'");
  } else {
    mysqli_query($conn, "INSERT INTO tabel_pengeluaran (jenis_pengeluaran,nama_alat,tanggal,nominal,keterangan) VALUES ('$jenis','$nama_alat','$tanggal',$nominal,'$keterangan')");
  }
  header('Location: pengeluaran.php'); exit;
}

include 'header.php';
?>
<h2>Laporan Pengeluaran</h2>
<div class="card mb-3"><div class="card-body">
<form method="post" class="row g-3">
  <input type="hidden" name="id" id="id">
  <div class="col-md-3"><label class="form-label">Jenis</label><select name="jenis_pengeluaran" id="jenis_pengeluaran" class="form-select"><option value="pembelian">pembelian</option><option value="maintenance">maintenance</option></select></div>
  <div class="col-md-3"><label class="form-label">Nama Alat</label><input name="nama_alat" id="nama_alat" class="form-control"></div>
  <div class="col-md-2"><label class="form-label">Tanggal</label><input type="date" name="tanggal" id="tanggal" class="form-control"></div>
  <div class="col-md-2"><label class="form-label">Nominal</label><input type="number" name="nominal" id="nominal" class="form-control"></div>
  <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" id="keterangan" class="form-control"></div>
  <div class="col-12"><button class="btn btn-success">Simpan</button><button type="reset" class="btn btn-secondary" onclick="clearForm()">Reset</button></div>
</form>
</div></div>

<div class="card"><div class="card-body">
<form method="get" class="row g-2 mb-3">
  <div class="col-md-3"><label>Filter Bulan</label><input type="month" name="bulan" class="form-control" value="<?=htmlspecialchars($_GET['bulan'] ?? '')?>"></div>
  <div class="col-md-2 align-self-end"><button class="btn btn-primary">Filter</button></div>
  <div class="col-md-3 align-self-end"><a class="btn btn-secondary" href="pengeluaran.php">Reset</a></div>
</form>

<?php
$where = '1=1';
if(!empty($_GET['bulan'])){
  $b = $_GET['bulan'];
  $where = "DATE_FORMAT(tanggal,'%Y-%m')='".mysqli_real_escape_string($conn,$b)."'";
}
$q = mysqli_query($conn, "SELECT * FROM tabel_pengeluaran WHERE $where ORDER BY tanggal DESC");
$total = 0;
?>
<table class="table table-striped"><thead><tr><th>#</th><th>Jenis</th><th>Nama Alat</th><th>Tanggal</th><th>Nominal</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>
<?php while($r=mysqli_fetch_assoc($q)){ $total += $r['nominal']; echo '<tr>'; echo '<td>'.$r['id_pengeluaran'].'</td>'; echo '<td>'.$r['jenis_pengeluaran'].'</td>'; echo '<td>'.htmlspecialchars($r['nama_alat']).'</td>'; echo '<td>'.$r['tanggal'].'</td>'; echo '<td>'.number_format($r['nominal'],0,',','.').'</td>'; echo '<td>'.htmlspecialchars($r['keterangan']).'</td>'; echo '<td><a class="btn btn-sm btn-danger" href="?delete='.$r['id_pengeluaran'].'" onclick="return confirm(\'Hapus?\')">Hapus</a></td>'; echo '</tr>'; } ?></tbody>
<tfoot><tr><th colspan="4">Total</th><th><?=number_format($total,0,',','.')?></th><th colspan="2"></th></tr></tfoot>
</table>
</div></div>

<script>
function clearForm(){ document.getElementById('id').value=''; }
</script>

<?php include 'footer.php'; ?>