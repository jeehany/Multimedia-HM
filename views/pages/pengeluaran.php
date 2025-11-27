<?php
require_once __DIR__ . '/../../config.php';
require_login();

if(can_edit() && isset($_GET['delete'])){ 
  $id=(int)$_GET['delete']; 
  mysqli_query($conn, "DELETE FROM tabel_pengeluaran WHERE id_pengeluaran = $id"); 
  header('Location: pengeluaran.php'); 
  exit; 
}

include __DIR__ . '/../../header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="fas fa-wallet me-2" style="color: #4f46e5;"></i>Laporan Pengeluaran</h2>
    <p class="mb-0">Kelola dan pantau semua pengeluaran operasional</p>
  </div>
  <?php if(can_edit()): ?>
  <a class="btn btn-primary" href="pengeluaran_add.php">
    <i class="fas fa-plus me-1"></i> Tambah Pengeluaran
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
          <input name="q" value="<?=htmlspecialchars($_GET['q'] ?? '')?>" class="form-control" placeholder="Cari nama atau keterangan...">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Bulan</label>
        <input type="month" name="bulan" class="form-control" value="<?=htmlspecialchars($_GET['bulan'] ?? '')?>">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Jenis</label>
        <select name="jenis" class="form-select">
          <option value="">Semua Jenis</option>
          <option value="pembelian" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='pembelian')?'selected':''?>>Pembelian</option>
          <option value="maintenance" <?=(!empty($_GET['jenis']) && $_GET['jenis']=='maintenance')?'selected':''?>>Maintenance</option>
        </select>
      </div>
      <div class="col-md-2 align-self-end">
        <a href="pengeluaran.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-1"></i> Reset</a>
      </div>
    </form>
  </div>
</div>

<?php
$where = '1=1';
if(!empty($_GET['q'])){
  $qstr = mysqli_real_escape_string($conn, $_GET['q']);
  $where .= " AND (nama_alat LIKE '%$qstr%' OR keterangan LIKE '%$qstr%')";
}
if(!empty($_GET['bulan'])){
  $b = $_GET['bulan'];
  $where .= " AND DATE_FORMAT(tanggal,'%Y-%m')='".mysqli_real_escape_string($conn,$b)."'";
}
if(!empty($_GET['jenis'])){
  $where .= " AND jenis_pengeluaran='".mysqli_real_escape_string($conn,$_GET['jenis'])."'";
}
$q = mysqli_query($conn, "SELECT * FROM tabel_pengeluaran WHERE $where ORDER BY tanggal DESC");
$total = 0;
?>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Jenis</th>
            <th>Nama Alat</th>
            <th>Tanggal</th>
            <th>Nominal</th>
            <th>Keterangan</th>
            <?php if(can_edit()): ?><th class="text-center">Aksi</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        while($r=mysqli_fetch_assoc($q)){ 
          $total += $r['nominal'];
          $jenis_badge = $r['jenis_pengeluaran'] === 'pembelian' ? 'bg-primary' : 'bg-secondary';
          echo '<tr>';
          echo '<td>'.$no++.'</td>';
          echo '<td><span class="badge '.$jenis_badge.'">'.ucfirst($r['jenis_pengeluaran']).'</span></td>';
          echo '<td><strong>'.htmlspecialchars($r['nama_alat']).'</strong></td>';
          echo '<td>'.date('d M Y', strtotime($r['tanggal'])).'</td>';
          echo '<td>Rp '.number_format($r['nominal'],0,',','.').'</td>';
          echo '<td>'.htmlspecialchars($r['keterangan']).'</td>';
          if(can_edit()) { 
            echo '<td class="text-center">
              <a class="btn btn-sm btn-primary me-1" href="pengeluaran_edit.php?id='.$r['id_pengeluaran'].'" title="Edit"><i class="fas fa-edit"></i></a>
              <a class="btn btn-sm btn-danger" href="?delete='.$r['id_pengeluaran'].'" onclick="return confirm(\'Hapus data ini?\')" title="Hapus"><i class="fas fa-trash"></i></a>
            </td>';
          } 
          echo '</tr>';
        }
        if(mysqli_num_rows($q) == 0){
          echo '<tr><td colspan="'.(can_edit()?7:6).'" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Tidak ada data ditemukan</td></tr>';
        }
        ?>
        </tbody>
        <tfoot style="background: #f8fafc;">
          <tr>
            <th colspan="4" class="text-end">Total Pengeluaran:</th>
            <th style="color: #4f46e5; font-size: 1.1rem;">Rp <?=number_format($total,0,',','.')?></th>
            <?php if(can_edit()): ?><th colspan="2"></th><?php else: ?><th></th><?php endif; ?>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
