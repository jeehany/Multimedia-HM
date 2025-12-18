<?php
require_once 'config.php';
require_login();

// summary counts (adjusted to new DB schema)
$counts = [];
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tools"); $counts['alat'] = mysqli_fetch_assoc($q)['c'] ?? 0;
$q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tools WHERE current_condition <> 'baik'"); $counts['rusak'] = mysqli_fetch_assoc($q)['c'] ?? 0;

include 'header.php';
?>

<div class="page-header">
  <h1><i class="fas fa-th-large me-2" style="color: #4f46e5;"></i>Dashboard</h1>
  <p>Selamat datang, <?=htmlspecialchars($_SESSION['user']['name'])?>! Berikut ringkasan data sistem.</p>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-6">
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
  <div class="col-md-6">
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
</div>

<div class="row g-4">
  <div class="col-md-12">
    <div class="card p-4">
      <h6 class="mb-3"><i class="fas fa-chart-pie me-2" style="color: #4f46e5;"></i>Distribusi Kondisi Alat</h6>
      <div class="chart-container">
        <canvas id="chartKondisi"></canvas>
      </div>
    </div>
  </div>
</div>

<?php
$kondisiQ = mysqli_query($conn, "SELECT current_condition AS kondisi, COUNT(*) as c FROM tools GROUP BY current_condition");
$kondisiLabels = [];
$kondisiData = [];
while($r = mysqli_fetch_assoc($kondisiQ)){
    $kondisiLabels[] = ucfirst($r['kondisi']);
    $kondisiData[] = (int)$r['c'];
}
// pengeluaran & permohonan features removed (no matching table in new schema)
?>

<script>
const kondisiLabels = <?=json_encode($kondisiLabels)?>;
const kondisiData = <?=json_encode($kondisiData)?>;
// months data removed

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

// pengeluaran chart removed
</script>

<?php include 'footer.php'; ?>
