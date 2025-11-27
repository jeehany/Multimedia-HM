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

<div class="page-header">
  <h1><i class="fas fa-th-large me-2" style="color: #4f46e5;"></i>Dashboard</h1>
  <p>Selamat datang, <?=htmlspecialchars($_SESSION['user']['nama_user'])?>! Berikut ringkasan data sistem.</p>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card card-summary p-4">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h6>Total Alat</h6>
          <h2><?=$counts['alat']?></h2>
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(79,70,229,0.1);">
          <i class="fas fa-box" style="color: #4f46e5; font-size: 1.25rem;"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-4" style="--accent: #ef4444;">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h6>Alat Rusak</h6>
          <h2><?=$counts['rusak']?></h2>
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239,68,68,0.1);">
          <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.25rem;"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-4" style="--accent: #f59e0b;">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h6>Permohonan</h6>
          <h2><?=$counts['permohonan']?></h2>
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245,158,11,0.1);">
          <i class="fas fa-clock" style="color: #f59e0b; font-size: 1.25rem;"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary p-4" style="--accent: #10b981;">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h6>Total Konten</h6>
          <h2><?=$counts['konten']?></h2>
        </div>
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16,185,129,0.1);">
          <i class="fas fa-photo-video" style="color: #10b981; font-size: 1.25rem;"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card p-4">
      <h6 class="mb-3"><i class="fas fa-chart-pie me-2" style="color: #4f46e5;"></i>Distribusi Kondisi Alat</h6>
      <div class="chart-container">
        <canvas id="chartKondisi"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-4">
      <h6 class="mb-3"><i class="fas fa-chart-bar me-2" style="color: #4f46e5;"></i>Pengeluaran 6 Bulan Terakhir</h6>
      <div class="chart-container">
        <canvas id="chartPengeluaran"></canvas>
      </div>
    </div>
  </div>
</div>

<?php
$kondisiQ = mysqli_query($conn, "SELECT kondisi, COUNT(*) as c FROM tabel_alat GROUP BY kondisi");
$kondisiLabels = [];
$kondisiData = [];
while($r = mysqli_fetch_assoc($kondisiQ)){
    $kondisiLabels[] = ucfirst($r['kondisi']);
    $kondisiData[] = (int)$r['c'];
}

$labels = [];
$values = [];
for($i=5;$i>=0;$i--){
    $m = date('Y-m', strtotime("-{$i} months"));
    $labels[] = date('M Y', strtotime($m.'-01'));
    $qq = mysqli_query($conn, "SELECT IFNULL(SUM(nominal),0) as s FROM tabel_pengeluaran WHERE DATE_FORMAT(tanggal,'%Y-%m') = '$m'");
    $values[] = (float)mysqli_fetch_assoc($qq)['s'];
}
?>

<script>
const kondisiLabels = <?=json_encode($kondisiLabels)?>;
const kondisiData = <?=json_encode($kondisiData)?>;
const monthsLabels = <?=json_encode($labels)?>;
const monthsValues = <?=json_encode($values)?>;

Chart.defaults.font.family = "'Inter', sans-serif";

const ctx1 = document.getElementById('chartKondisi');
if(ctx1){
  new Chart(ctx1, {
    type:'doughnut', 
    data:{
      labels:kondisiLabels, 
      datasets:[{
        data:kondisiData, 
        backgroundColor:['#10b981','#f59e0b','#ef4444'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { padding: 20, usePointStyle: true }
        }
      },
      cutout: '65%'
    }
  });
}

const ctx2 = document.getElementById('chartPengeluaran');
if(ctx2){
  new Chart(ctx2, {
    type:'bar', 
    data:{
      labels:monthsLabels, 
      datasets:[{
        label:'Pengeluaran (Rp)', 
        data:monthsValues, 
        backgroundColor:'#4f46e5',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: '#e2e8f0' },
          ticks: { 
            callback: function(value) {
              return 'Rp ' + value.toLocaleString('id-ID');
            }
          }
        },
        x: {
          grid: { display: false }
        }
      }
    }
  });
}
</script>

<?php include 'footer.php'; ?>
