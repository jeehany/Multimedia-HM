<?php
require_once 'config.php';
require_login();

// admin delete handler
if(can_edit() && isset($_GET['delete'])){ $id=(int)$_GET['delete']; mysqli_query($conn, "DELETE FROM tabel_pengeluaran WHERE id_pengeluaran = $id"); header('Location: pengeluaran.php'); exit; }

include 'header.php';
?>
<h2>Laporan Pengeluaran</h2>

<?php if(can_edit()): ?>
<div class="card mb-3"><div class="card-body">
<form method="get" class="row g-2">
  <div class="col-md-3"><label>Filter Bulan</label><input type="month" name="bulan" class="form-control" value="<?=htmlspecialchars($_GET['bulan'] ?? '')?>"></div>
  <div class="col-md-3 align-self-end"><a class="btn btn-success" href="pengeluaran_add.php"><i class="fa fa-plus"></i> Tambah</a></div>
  <div class="col-md-3 align-self-end"><a class="btn btn-secondary" href="pengeluaran.php">Reset</a></div>
</form>
</div></div>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-eye"></i> Mode Baca Saja (Read-Only)</div>
<?php endif; ?>

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
<table class="table table-striped"><thead><tr><th>#</th><th>Jenis</th><th>Nama Alat</th><th>Tanggal</th><th>Nominal</th><th>Keterangan</th><?php if(can_edit()): ?><th>Aksi</th><?php endif; ?></tr></thead><tbody>
<?php while($r=mysqli_fetch_assoc($q)){ $total += $r['nominal']; echo '<tr>'; echo '<td>'.$r['id_pengeluaran'].'</td>'; echo '<td>'.$r['jenis_pengeluaran'].'</td>'; echo '<td>'.htmlspecialchars($r['nama_alat']).'</td>'; echo '<td>'.$r['tanggal'].'</td>'; echo '<td>'.number_format($r['nominal'],0,',','.').'</td>'; echo '<td>'.htmlspecialchars($r['keterangan']).'</td>'; if(can_edit()) { echo '<td><a class="btn btn-sm btn-primary me-1" href="pengeluaran_edit.php?id='.$r['id_pengeluaran'].'"><i class="fa fa-edit"></i> Edit</a><a class="btn btn-sm btn-danger" href="?delete='.$r['id_pengeluaran'].'" onclick="return confirm(\'Hapus?\')"><i class="fa fa-trash"></i> Hapus</a></td>'; } echo '</tr>'; } ?></tbody>
<tfoot><tr><th colspan="4">Total</th><th><?=number_format($total,0,',','.')?></th><?php if(can_edit()): ?><th colspan="2"></th><?php else: ?><th></th><?php endif; ?></tr></tfoot>
</table>
</div></div>

<?php include 'footer.php'; ?>