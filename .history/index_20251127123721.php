<?php
require_once 'config.php';
require_login();

// summary counts
$counts = [];
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_alat"); $counts['alat'] = mysqli_fetch_assoc($q)['c'] ?? 0;
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_alat WHERE kondisi <> 'baik'"); $counts['rusak'] = mysqli_fetch_assoc($q)['c'] ?? 0;
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_pembelian WHERE status='menunggu'"); $counts['permohonan'] = mysqli_fetch_assoc($q)['c'] ?? 0;
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_pengeluaran"); $counts['pengeluaran_count'] = mysqli_fetch_assoc($q)['c'] ?? 0;
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_konten"); $counts['konten'] = mysqli_fetch_assoc($q)['c'] ?? 0;

include 'header.php';
?>
<h1>Dashboard</h1>
<div class="row">
  <div class="col-md-3">
    <div class="card card-summary p-3 mb-3">
      <h6>Jumlah Alat</h6>
      <h2><?=$counts['alat']?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-3 mb-3">
      <h6>Alat Rusak</h6>
      <h2><?=$counts['rusak']?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-3 mb-3">
      <h6>Permohonan</h6>
      <h2><?=$counts['permohonan']?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-3 mb-3">
      <h6>Konten</h6>
      <h2><?=$counts['konten']?></h2>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card mb-3 p-3">
      <h6>Distribusi Kondisi Alat</h6>
      <canvas id="chartKondisi"></canvas>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card mb-3 p-3">
      <h6>Pengeluaran per Bulan (Terakhir 6 bulan)</h6>
      <canvas id="chartPengeluaran"></canvas>
    </div>
  </div>
</div>

<?php // data for charts
$kondisiQ = mysqli_query($conn, "SELECT kondisi, COUNT(*) as c FROM tabel_alat GROUP BY kondisi");
$kondisiLabels = [];
$kondisiData = [];
while($r = mysqli_fetch_assoc($kondisiQ)){
    $kondisiLabels[] = $r['kondisi'];
    $kondisiData[] = (int)$r['c'];
}

// pengeluaran per bulan last 6 months
$labels = [];
$values = [];
for($i=5;$i>=0;$i--){
    $m = date('Y-m', strtotime("-{$i} months"));
    $labels[] = $m;
    $qq = mysqli_query($conn, "SELECT IFNULL(SUM(nominal),0) as s FROM tabel_pengeluaran WHERE DATE_FORMAT(tanggal,'%Y-%m') = '$m'");
    $values[] = (float)mysqli_fetch_assoc($qq)['s'];
}
?>

<script>
const kondisiLabels = <?=json_encode($kondisiLabels)?>;
const kondisiData = <?=json_encode($kondisiData)?>;
const monthsLabels = <?=json_encode($labels)?>;
const monthsValues = <?=json_encode($values)?>;

const ctx1 = document.getElementById('chartKondisi');
if(ctx1){
  new Chart(ctx1, {type:'doughnut', data:{labels:kondisiLabels, datasets:[{data:kondisiData, backgroundColor:['#198754','#ffc107','#dc3545']}]}});
}
const ctx2 = document.getElementById('chartPengeluaran');
if(ctx2){
  new Chart(ctx2, {type:'bar', data:{labels:monthsLabels, datasets:[{label:'Pengeluaran', data:monthsValues, backgroundColor:'#0d6efd'}]}});
}
</script>

<?php include 'footer.php'; ?>
