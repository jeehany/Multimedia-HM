<?php
require_once __DIR__ . '/../config.php';
require_login();

// ====== Helper functions untuk perhitungan aggregation ======
function get_alat_summary() {
  global $conn;
  $summary = [];
  $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM tools");
  $summary['total'] = mysqli_fetch_assoc($q)['total'] ?? 0;

  $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tools WHERE current_condition = 'baik'");
  $summary['baik'] = mysqli_fetch_assoc($q)['c'] ?? 0;

  $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tools WHERE current_condition IN ('rusak ringan','rusak berat')");
  $summary['rusak'] = mysqli_fetch_assoc($q)['c'] ?? 0;

  $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tools WHERE current_condition = 'perlu perbaikan'");
  $summary['perlu_perbaikan'] = mysqli_fetch_assoc($q)['c'] ?? 0;

  return $summary;
}

function get_alat_per_lokasi() {
  // not available in new schema (no `lokasi` column on `tools`)
  return [];
}

function get_maintenance_total($where = '') {
  global $conn;
  $sql = "SELECT IFNULL(SUM(repair_cost), 0) as total FROM tool_maintenances";
  if($where) $sql .= " WHERE $where";
  $q = mysqli_query($conn, $sql);
  return mysqli_fetch_assoc($q)['total'] ?? 0;
}

function get_pembelian_count($where = '') {
  global $conn;
  $sql = "SELECT COUNT(*) as cnt FROM purchases";
  if($where) $sql .= " WHERE $where";
  $q = mysqli_query($conn, $sql);
  return mysqli_fetch_assoc($q)['cnt'] ?? 0;
}

function get_pengeluaran_total($where = '') {
  // pengeluaran not available in new schema
  return 0;
}

// CSV Export handler
if(isset($_GET['export'])){
    $type = $_GET['export'];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_'.$type.'_'.date('Y-m-d').'.csv"');
    
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
    
    if($type==='alat'){
        // Summary header
        $summary = get_alat_summary();
        fputcsv($out, ['LAPORAN DATA ALAT MULTIMEDIA']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Alat', $summary['total']]);
        fputcsv($out, ['Kondisi Baik', $summary['baik']]);
        fputcsv($out, ['Kondisi Rusak', $summary['rusak']]);
        fputcsv($out, ['Perlu Perbaikan', $summary['perlu_perbaikan']]);
        fputcsv($out, []);
        fputcsv($out, ['REKAP PER LOKASI']);
        $lokasi_data = get_alat_per_lokasi();
        foreach($lokasi_data as $l) {
            fputcsv($out, [$l['lokasi'], $l['cnt']]);
        }
        fputcsv($out, []);
        fputcsv($out, ['DETAIL DATA ALAT']);
        fputcsv($out, ['ID','Tool Code','Tool Name','Condition','Availability','Created At']);
        $q=mysqli_query($conn,"SELECT id, tool_code, tool_name, current_condition, availability_status, created_at FROM tools ORDER BY id"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id'],$r['tool_code'],$r['tool_name'],$r['current_condition'],$r['availability_status'],$r['created_at']]);
    } elseif($type==='maintenance'){
        $total_biaya = get_maintenance_total();
        fputcsv($out, ['LAPORAN MAINTENANCE ALAT']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Biaya Maintenance', number_format($total_biaya, 2, '.', '')]);
        fputcsv($out, []);
        fputcsv($out, ['DETAIL MAINTENANCE']);
        fputcsv($out, ['ID','Alat','Type','Tanggal','Repaired By','Repair Cost','Handled By']);
        $q=mysqli_query($conn,"SELECT tm.id, t.tool_name, mt.type_name, tm.maintenance_date, tm.repaired_by, tm.repair_cost, tm.handled_by FROM tool_maintenances tm LEFT JOIN tools t ON tm.tool_id=t.id LEFT JOIN maintenance_types mt ON tm.maintenance_type_id=mt.id ORDER BY tm.id"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id'],$r['tool_name'],$r['type_name'],$r['maintenance_date'],$r['repaired_by'],$r['repair_cost'],$r['handled_by']]);
    } elseif($type==='pembelian'){
        $total_pembelian = get_pembelian_count();
        fputcsv($out, ['LAPORAN PERMOHONAN PEMBELIAN']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Permohonan', $total_pembelian]);
        fputcsv($out, []);
        fputcsv($out, ['DETAIL PEMBELIAN']);
        fputcsv($out, ['ID','Tanggal','Vendor','Total Cost','Created By']);
        $q=mysqli_query($conn,"SELECT p.id, p.purchase_date, v.vendor_name, p.total_cost, p.created_by FROM purchases p LEFT JOIN vendors v ON p.vendor_id=v.id ORDER BY p.id"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id'],$r['purchase_date'],$r['vendor_name'],$r['total_cost'],$r['created_by']]);
    } elseif($type==='pengeluaran'){
        // pengeluaran not available in new schema
        fputcsv($out, ['LAPORAN PENGELUARAN TIDAK TERSEDIA']);
        fputcsv($out, ['Alasan: tidak ada tabel `pengeluaran` pada skema database terbaru.']);
    } elseif($type==='konten'){
        // konten not available in new schema
        fputcsv($out, ['LAPORAN KONTEN TIDAK TERSEDIA']);
        fputcsv($out, ['Alasan: tidak ada tabel `konten` pada skema database terbaru.']);
    }
    fclose($out);
    exit;
}

include __DIR__ . '/../header.php';

// Determine which report to show
$report = $_GET['report'] ?? 'index';
?>

<?php if($report === 'index'): ?>

<div class="page-header">
  <h2><i class="fas fa-file-alt me-2" style="color: #4f46e5;"></i>Halaman Laporan</h2>
  <p>Pilih jenis laporan yang ingin ditampilkan atau diunduh</p>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(79,70,229,0.1);">
            <i class="fas fa-box" style="color: #4f46e5; font-size: 1.25rem;"></i>
          </div>
          <h5 class="card-title mb-0">Laporan Data Alat</h5>
        </div>
        <p class="card-text text-muted mb-3">Ringkasan alat multimedia, kondisi, dan distribusi per lokasi.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="laporan.php?report=alat"><i class="fas fa-eye me-1"></i> Lihat</a>
          <a class="btn btn-success" href="laporan.php?export=alat"><i class="fas fa-download me-1"></i> CSV</a>
          <button class="btn btn-secondary" onclick="window.open('laporan.php?report=alat&print=1','_blank')"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(245,158,11,0.1);">
            <i class="fas fa-wrench" style="color: #f59e0b; font-size: 1.25rem;"></i>
          </div>
          <h5 class="card-title mb-0">Laporan Maintenance</h5>
        </div>
        <p class="card-text text-muted mb-3">Riwayat perawatan alat dan total biaya maintenance.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="laporan.php?report=maintenance"><i class="fas fa-eye me-1"></i> Lihat</a>
          <a class="btn btn-success" href="laporan.php?export=maintenance"><i class="fas fa-download me-1"></i> CSV</a>
          <button class="btn btn-secondary" onclick="window.open('laporan.php?report=maintenance&print=1','_blank')"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(16,185,129,0.1);">
            <i class="fas fa-shopping-cart" style="color: #10b981; font-size: 1.25rem;"></i>
          </div>
          <h5 class="card-title mb-0">Laporan Pembelian</h5>
        </div>
        <p class="card-text text-muted mb-3">Daftar permohonan pembelian alat dan status persetujuan.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="laporan.php?report=pembelian"><i class="fas fa-eye me-1"></i> Lihat</a>
          <a class="btn btn-success" href="laporan.php?export=pembelian"><i class="fas fa-download me-1"></i> CSV</a>
          <button class="btn btn-secondary" onclick="window.open('laporan.php?report=pembelian&print=1','_blank')"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(239,68,68,0.1);">
            <i class="fas fa-wallet" style="color: #ef4444; font-size: 1.25rem;"></i>
          </div>
          <h5 class="card-title mb-0">Laporan Pengeluaran</h5>
        </div>
        <p class="card-text text-muted mb-3">Daftar pengeluaran dari pembelian dan maintenance.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="laporan.php?report=pengeluaran"><i class="fas fa-eye me-1"></i> Lihat</a>
          <a class="btn btn-success" href="laporan.php?export=pengeluaran"><i class="fas fa-download me-1"></i> CSV</a>
          <button class="btn btn-secondary" onclick="window.open('laporan.php?report=pengeluaran&print=1','_blank')"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(139,92,246,0.1);">
            <i class="fas fa-photo-video" style="color: #8b5cf6; font-size: 1.25rem;"></i>
          </div>
          <h5 class="card-title mb-0">Laporan Konten</h5>
        </div>
        <p class="card-text text-muted mb-3">Daftar konten multimedia yang telah diunggah.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-primary" href="laporan.php?report=konten"><i class="fas fa-eye me-1"></i> Lihat</a>
          <a class="btn btn-success" href="laporan.php?export=konten"><i class="fas fa-download me-1"></i> CSV</a>
          <button class="btn btn-secondary" onclick="window.open('laporan.php?report=konten&print=1','_blank')"><i class="fas fa-print me-1"></i> Print</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php elseif($report === 'alat'): ?>
<h2><i class="fa fa-box"></i> Laporan Data Alat Multimedia</h2>
<?php
$summary = get_alat_summary();
$lokasi_data = get_alat_per_lokasi();
?>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h6 class="card-title">Total Alat</h6>
        <h2><?= $summary['total'] ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-success text-white">
      <div class="card-body">
        <h6 class="card-title">Kondisi Baik</h6>
        <h2><?= $summary['baik'] ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <h6 class="card-title">Kondisi Rusak</h6>
        <h2><?= $summary['rusak'] ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-danger text-white">
      <div class="card-body">
        <h6 class="card-title">Perlu Perbaikan</h6>
        <h2><?= $summary['perlu_perbaikan'] ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="row mb-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Distribusi Kondisi Alat</h6>
        <canvas id="chartKondisiAlatReport"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Distribusi Alat Per Lokasi</h6>
        <canvas id="chartLokasi"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Table -->
<div class="card mb-3">
  <div class="card-body">
    <h5>Detail Data Alat</h5>
    <table class="table table-striped table-sm">
      <thead>
        <tr><th>#</th><th>Nama</th><th>Jenis</th><th>Kondisi</th><th>Lokasi</th><th>PJ</th><th>Tgl Pembelian</th></tr>
      </thead>
      <tbody>
        <?php $q=mysqli_query($conn,"SELECT id, tool_code, tool_name, current_condition, availability_status, created_at FROM tools ORDER BY id"); 
        while($r=mysqli_fetch_assoc($q)) {
          echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['tool_name']).'</td><td>'.htmlspecialchars($r['tool_code']).'</td><td>'.$r['current_condition'].'</td><td>'.htmlspecialchars($r['availability_status']).'</td><td>'.htmlspecialchars('N/A').'</td><td>'.$r['created_at'].'</td></tr>';
        } ?>
      </tbody>
    </table>
  </div>
</div>

<a class="btn btn-secondary" href="laporan.php">Kembali</a>

<?php
// Data untuk chart
$kondisiQ = mysqli_query($conn, "SELECT current_condition AS kondisi, COUNT(*) as c FROM tools GROUP BY current_condition");
$kondisiLabels = [];
$kondisiData = [];
while($r = mysqli_fetch_assoc($kondisiQ)){
    $kondisiLabels[] = $r['kondisi'];
    $kondisiData[] = (int)$r['c'];
}

$lokasiLabels = [];
$lokasiData = [];
foreach($lokasi_data as $l) {
    $lokasiLabels[] = $l['lokasi'];
    $lokasiData[] = $l['cnt'];
}
?>

<script>
const kondisiLabelsAlatReport = <?=json_encode($kondisiLabels)?>;
const kondisiDataAlatReport = <?=json_encode($kondisiData)?>;
const lokasiLabels = <?=json_encode($lokasiLabels)?>;
const lokasiData = <?=json_encode($lokasiData)?>;

if(document.getElementById('chartKondisiAlatReport')){
  new Chart(document.getElementById('chartKondisiAlatReport'), {
    type: 'doughnut',
    data: {
      labels: kondisiLabelsAlatReport,
      datasets: [{
        data: kondisiDataAlatReport,
        backgroundColor: ['#198754', '#ffc107', '#dc3545']
      }]
    }
  });
}

if(document.getElementById('chartLokasi')){
  new Chart(document.getElementById('chartLokasi'), {
    type: 'bar',
    data: {
      labels: lokasiLabels,
      datasets: [{
        label: 'Jumlah Alat',
        data: lokasiData,
        backgroundColor: '#0d6efd'
      }]
    },
    options: { indexAxis: 'y', maintainAspectRatio: true }
  });
}
</script>

<?php elseif($report === 'maintenance'): ?>
<h2><i class="fa fa-tools"></i> Laporan Maintenance Alat</h2>
<?php
$total_biaya = get_maintenance_total();
?>

<!-- Summary Card -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-info text-white">
      <div class="card-body">
        <h6 class="card-title">Total Biaya Maintenance</h6>
        <h2><?= number_format($total_biaya, 0, ',', '.') ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Table -->
<div class="card mb-3">
  <div class="card-body">
    <h5>Daftar Maintenance</h5>
    <table class="table table-striped table-sm">
      <thead>
        <tr><th>#</th><th>Alat</th><th>Jenis</th><th>Tanggal</th><th>Teknisi</th><th>Biaya</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php 
        $q=mysqli_query($conn,"SELECT tm.id, t.tool_name, mt.type_name, tm.maintenance_date, tm.repaired_by, tm.repair_cost FROM tool_maintenances tm LEFT JOIN tools t ON tm.tool_id=t.id LEFT JOIN maintenance_types mt ON tm.maintenance_type_id=mt.id ORDER BY tm.id DESC");
        while($r=mysqli_fetch_assoc($q)) {
          echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['tool_name']).'</td><td>'.htmlspecialchars($r['type_name']).'</td><td>'.$r['maintenance_date'].'</td><td>'.htmlspecialchars($r['repaired_by']).'</td><td>'.number_format($r['repair_cost'],0,',','.').'</td><td>'.htmlspecialchars('N/A').'</td></tr>';
        } 
        ?>
      </tbody>
      <tfoot>
        <tr><th colspan="5">Total Biaya</th><th><?= number_format($total_biaya, 0, ',', '.') ?></th><th></th></tr>
      </tfoot>
    </table>
  </div>
</div>

<a class="btn btn-secondary" href="laporan.php">Kembali</a>

<?php elseif($report === 'pembelian'): ?>
<h2><i class="fa fa-cart-plus"></i> Laporan Permohonan Pembelian</h2>
<?php
$total_pembelian = get_pembelian_count();
?>

<!-- Summary Card -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <h6 class="card-title">Total Permohonan</h6>
        <h2><?= $total_pembelian ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Table -->
<div class="card mb-3">
  <div class="card-body">
    <h5>Daftar Permohonan Pembelian</h5>
    <table class="table table-striped table-sm">
      <thead>
        <tr><th>#</th><th>Nama Alat</th><th>Alasan</th><th>Estimasi</th><th>Tanggal</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php 
        $q=mysqli_query($conn,"SELECT p.id, p.purchase_date, v.vendor_name, p.total_cost, p.created_by FROM purchases p LEFT JOIN vendors v ON p.vendor_id=v.id ORDER BY p.id DESC");
        while($r=mysqli_fetch_assoc($q)) {
          echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['purchase_date']).'</td><td>'.htmlspecialchars($r['vendor_name']).'</td><td>'.number_format($r['total_cost'],0,',','.').'</td><td>'.htmlspecialchars($r['created_by']).'</td></tr>';
        } 
        ?>
      </tbody>
      <tfoot>
        <tr><th colspan="5">Total Permohonan</th><th><?= $total_pembelian ?></th></tr>
      </tfoot>
    </table>
  </div>
</div>

<a class="btn btn-secondary" href="laporan.php">Kembali</a>

<?php elseif($report === 'pengeluaran'): ?>
<h2><i class="fa fa-money-bill"></i> Laporan Pengeluaran Alat Multimedia</h2>

<!-- Filter Form -->
<div class="card mb-3">
  <div class="card-body">
    <form method="get" class="row g-2">
      <input type="hidden" name="report" value="pengeluaran">
      <div class="col-md-3"><label>Filter Bulan</label><input type="month" name="bulan" class="form-control" value="<?=htmlspecialchars($_GET['bulan'] ?? '')?>"></div>
      <div class="col-md-2 align-self-end"><button class="btn btn-primary">Filter</button></div>
      <div class="col-md-3 align-self-end"><a class="btn btn-secondary" href="laporan.php?report=pengeluaran">Reset</a></div>
    </form>
  </div>
</div>

<?php
$where = '1=1';
if(!empty($_GET['bulan'])){
  $b = $_GET['bulan'];
  $where = "DATE_FORMAT(tanggal,'%Y-%m')='".mysqli_real_escape_string($conn,$b)."'";
}
$total_pengeluaran = get_pengeluaran_total($where);
?>

<!-- Summary Card -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card bg-danger text-white">
      <div class="card-body">
        <h6 class="card-title">Total Pengeluaran</h6>
        <h2><?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Table -->
<div class="card mb-3">
  <div class="card-body">
    <h5>Daftar Pengeluaran</h5>
    <table class="table table-striped table-sm">
      <thead>
        <tr><th>#</th><th>Jenis</th><th>Nama Alat</th><th>Tanggal</th><th>Nominal</th><th>Keterangan</th></tr>
      </thead>
      <tbody>
        // pengeluaran tidak tersedia
        echo '<tr><td colspan="6">Laporan pengeluaran tidak tersedia pada skema terbaru.</td></tr>';
      </tbody>
      <tfoot>
        <tr><th colspan="4">Total Pengeluaran</th><th><?= number_format($total_pengeluaran, 0, ',', '.') ?></th><th></th></tr>
      </tfoot>
    </table>
  </div>
</div>

<a class="btn btn-secondary" href="laporan.php">Kembali</a>

<?php elseif($report === 'konten'): ?>
<h2><i class="fa fa-photo-video"></i> Laporan Konten Multimedia</h2>

<!-- Table -->
<div class="card mb-3">
  <div class="card-body">
    <h5>Daftar Konten</h5>
    <table class="table table-striped table-sm">
      <thead>
        <tr><th>#</th><th>Judul</th><th>Jenis</th><th>Deskripsi</th><th>PJ</th><th>Tanggal Upload</th><th>File</th></tr>
      </thead>
      <tbody>
        <?php 
        // konten tidak tersedia pada skema baru
        echo '<tr><td colspan="7">Laporan konten tidak tersedia karena tabel konten telah dihapus pada skema terbaru.</td></tr>';
        ?>
      </tbody>
    </table>
  </div>
</div>

<a class="btn btn-secondary" href="laporan.php">Kembali</a>

<?php endif; ?>

<?php
// Printable views
if(isset($_GET['print'])){
    $type = $_GET['report'] ?? $_GET['print'];
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Print Report</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>body{background:white;}</style></head><body class="p-4">';
    echo '<h3>Laporan '.htmlspecialchars($type).'</h3>';
    echo '<p><strong>Tanggal Export:</strong> '.date('Y-m-d H:i:s').'</p><hr>';
    
    if($type==='alat'){
        $summary = get_alat_summary();
        $lokasi_data = get_alat_per_lokasi();
        echo '<h5>RINGKASAN</h5>';
        echo '<table class="table table-sm"><tr><td>Total Alat</td><td><strong>'.$summary['total'].'</strong></td></tr>';
        echo '<tr><td>Kondisi Baik</td><td><strong>'.$summary['baik'].'</strong></td></tr>';
        echo '<tr><td>Kondisi Rusak</td><td><strong>'.$summary['rusak'].'</strong></td></tr>';
        echo '<tr><td>Perlu Perbaikan</td><td><strong>'.$summary['perlu_perbaikan'].'</strong></td></tr></table>';
        
        echo '<h5>REKAP PER LOKASI</h5>';
        echo '<table class="table table-sm table-bordered"><thead><tr><th>Lokasi</th><th>Jumlah Alat</th></tr></thead><tbody>';
        foreach($lokasi_data as $l) {
            echo '<tr><td>'.htmlspecialchars($l['lokasi']).'</td><td>'.$l['cnt'].'</td></tr>';
        }
        echo '</tbody></table>';
        
        echo '<h5>DETAIL DATA ALAT</h5>';
        echo '<table class="table table-bordered table-sm"><thead><tr><th>ID</th><th>Nama</th><th>Jenis</th><th>Kondisi</th><th>Lokasi</th><th>PJ</th><th>Tgl</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT id, tool_code, tool_name, current_condition, availability_status, created_at FROM tools"); 
        while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['tool_name']).'</td><td>'.htmlspecialchars($r['tool_code']).'</td><td>'.$r['current_condition'].'</td><td>'.htmlspecialchars($r['availability_status']).'</td><td>'.htmlspecialchars('N/A').'</td><td>'.$r['created_at'].'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='maintenance'){
        $total_biaya = get_maintenance_total();
        echo '<h5>RINGKASAN</h5>';
        echo '<table class="table table-sm"><tr><td>Total Biaya Maintenance</td><td><strong>'.number_format($total_biaya, 0, ',', '.').'</strong></td></tr></table>';
        
        echo '<h5>DETAIL MAINTENANCE</h5>';
        echo '<table class="table table-bordered table-sm"><thead><tr><th>ID</th><th>Alat</th><th>Jenis</th><th>Tanggal</th><th>Teknisi</th><th>Biaya</th><th>Status</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT tm.id, t.tool_name, mt.type_name, tm.maintenance_date, tm.repaired_by, tm.repair_cost FROM tool_maintenances tm LEFT JOIN tools t ON tm.tool_id=t.id LEFT JOIN maintenance_types mt ON tm.maintenance_type_id=mt.id ORDER BY tm.id"); 
        while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['tool_name']).'</td><td>'.htmlspecialchars($r['type_name']).'</td><td>'.$r['maintenance_date'].'</td><td>'.htmlspecialchars($r['repaired_by']).'</td><td>'.number_format($r['repair_cost'],0,',','.').'</td><td>'.htmlspecialchars('N/A').'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='pembelian'){
        $total_pembelian = get_pembelian_count();
        echo '<h5>RINGKASAN</h5>';
        echo '<table class="table table-sm"><tr><td>Total Permohonan</td><td><strong>'.$total_pembelian.'</strong></td></tr></table>';
        
        echo '<h5>DETAIL PERMOHONAN PEMBELIAN</h5>';
        echo '<table class="table table-bordered table-sm"><thead><tr><th>ID</th><th>Nama</th><th>Alasan</th><th>Estimasi</th><th>Tgl</th><th>Status</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT p.id, p.purchase_date, v.vendor_name, p.total_cost, p.created_by FROM purchases p LEFT JOIN vendors v ON p.vendor_id=v.id"); 
        while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id'].'</td><td>'.htmlspecialchars($r['vendor_name']).'</td><td>'.htmlspecialchars($r['purchase_date']).'</td><td>'.number_format($r['total_cost'],0,',','.').'</td><td>'.htmlspecialchars($r['created_by']).'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='pengeluaran'){
        $total_pengeluaran = get_pengeluaran_total();
        echo '<h5>RINGKASAN</h5>';
        echo '<table class="table table-sm"><tr><td>Total Pengeluaran</td><td><strong>'.number_format($total_pengeluaran, 0, ',', '.').'</strong></td></tr></table>';
        
        echo '<h5>DETAIL PENGELUARAN</h5>';
        echo '<table class="table table-bordered table-sm"><thead><tr><th>ID</th><th>Jenis</th><th>Nama Alat</th><th>Tanggal</th><th>Nominal</th><th>Keterangan</th></tr></thead><tbody>';
        $tot = 0; 
        // pengeluaran tidak tersedia pada skema terbaru
        echo '<tr><td colspan="6">Laporan pengeluaran tidak tersedia pada skema terbaru.</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='konten'){
        echo '<h5>DETAIL KONTEN MULTIMEDIA</h5>';
        echo '<table class="table table-bordered table-sm"><thead><tr><th>ID</th><th>Judul</th><th>Jenis</th><th>PJ</th><th>Tgl</th><th>File</th></tr></thead><tbody>';
        // konten tidak tersedia pada skema terbaru
        echo '<tr><td colspan="6">Laporan konten tidak tersedia pada skema terbaru.</td></tr>';
        echo '</tbody></table>';
    }
    echo '<script>window.print();</script></body></html>';
    exit;
}

include __DIR__ . '/../footer.php';
?>
