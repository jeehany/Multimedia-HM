<?php
require_once 'config.php';
require_login();

// ====== Helper functions untuk perhitungan aggregation ======
function get_alat_summary() {
    global $conn;
    $summary = [];
    $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM tabel_alat");
    $summary['total'] = mysqli_fetch_assoc($q)['total'] ?? 0;
    
    $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_alat WHERE kondisi = 'baik'");
    $summary['baik'] = mysqli_fetch_assoc($q)['c'] ?? 0;
    
    $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_alat WHERE kondisi IN ('rusak ringan', 'rusak berat')");
    $summary['rusak'] = mysqli_fetch_assoc($q)['c'] ?? 0;
    
    $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tabel_alat WHERE kondisi = 'perlu perbaikan'");
    $summary['perlu_perbaikan'] = mysqli_fetch_assoc($q)['c'] ?? 0;
    
    return $summary;
}

function get_alat_per_lokasi() {
    global $conn;
    $result = [];
    $q = mysqli_query($conn, "SELECT lokasi, COUNT(*) as cnt FROM tabel_alat WHERE lokasi IS NOT NULL AND lokasi != '' GROUP BY lokasi ORDER BY cnt DESC");
    while($r = mysqli_fetch_assoc($q)) {
        $result[] = $r;
    }
    return $result;
}

function get_maintenance_total($where = '') {
    global $conn;
    $sql = "SELECT IFNULL(SUM(biaya), 0) as total FROM tabel_maintenance";
    if($where) $sql .= " WHERE $where";
    $q = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($q)['total'] ?? 0;
}

function get_pembelian_count($where = '') {
    global $conn;
    $sql = "SELECT COUNT(*) as cnt FROM tabel_pembelian";
    if($where) $sql .= " WHERE $where";
    $q = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($q)['cnt'] ?? 0;
}

function get_pengeluaran_total($where = '') {
    global $conn;
    $sql = "SELECT IFNULL(SUM(nominal), 0) as total FROM tabel_pengeluaran";
    if($where) $sql .= " WHERE $where";
    $q = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($q)['total'] ?? 0;
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
        fputcsv($out, ['ID','Nama','Jenis','Kondisi','Lokasi','PJ','Tgl Pembelian']);
        $q=mysqli_query($conn,"SELECT * FROM tabel_alat ORDER BY id_alat"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id_alat'],$r['nama_alat'],$r['jenis'],$r['kondisi'],$r['lokasi'],$r['penanggung_jawab'],$r['tanggal_pembelian']]);
    } elseif($type==='maintenance'){
        $total_biaya = get_maintenance_total();
        fputcsv($out, ['LAPORAN MAINTENANCE ALAT']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Biaya Maintenance', number_format($total_biaya, 2, '.', '')]);
        fputcsv($out, []);
        fputcsv($out, ['DETAIL MAINTENANCE']);
        fputcsv($out, ['ID','Alat','Jenis','Tanggal','Teknisi','Biaya','Status']);
        $q=mysqli_query($conn,"SELECT m.*, a.nama_alat FROM tabel_maintenance m LEFT JOIN tabel_alat a ON m.id_alat=a.id_alat ORDER BY m.id_maintenance"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id_maintenance'],$r['nama_alat'],$r['jenis_maintenance'],$r['tanggal'],$r['teknisi'],$r['biaya'],$r['status']]);
    } elseif($type==='pembelian'){
        $total_pembelian = get_pembelian_count();
        fputcsv($out, ['LAPORAN PERMOHONAN PEMBELIAN']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Permohonan', $total_pembelian]);
        fputcsv($out, []);
        fputcsv($out, ['DETAIL PERMOHONAN']);
        fputcsv($out, ['ID','Nama Alat','Alasan','Estimasi','Tanggal','Status']);
        $q=mysqli_query($conn,"SELECT * FROM tabel_pembelian ORDER BY id_pembelian"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id_pembelian'],$r['nama_alat'],$r['alasan'],$r['estimasi_biaya'],$r['tanggal_permohonan'],$r['status']]);
    } elseif($type==='pengeluaran'){
        $total_pengeluaran = get_pengeluaran_total();
        fputcsv($out, ['LAPORAN PENGELUARAN ALAT MULTIMEDIA']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['RINGKASAN']);
        fputcsv($out, ['Total Pengeluaran', number_format($total_pengeluaran, 2, '.', '')]);
        fputcsv($out, []);
        fputcsv($out, ['DETAIL PENGELUARAN']);
        fputcsv($out, ['ID','Jenis','Nama Alat','Tanggal','Nominal','Keterangan']);
        $q=mysqli_query($conn,"SELECT * FROM tabel_pengeluaran ORDER BY tanggal DESC"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id_pengeluaran'],$r['jenis_pengeluaran'],$r['nama_alat'],$r['tanggal'],$r['nominal'],$r['keterangan']]);
    } elseif($type==='konten'){
        fputcsv($out, ['LAPORAN KONTEN MULTIMEDIA']);
        fputcsv($out, ['Tanggal Export', date('Y-m-d H:i:s')]);
        fputcsv($out, []);
        fputcsv($out, ['ID','Judul','Jenis','Deskripsi','PJ','Tanggal Upload','File Path']);
        $q=mysqli_query($conn,"SELECT * FROM tabel_konten ORDER BY id_konten"); 
        while($r=mysqli_fetch_assoc($q)) fputcsv($out, [$r['id_konten'],$r['judul'],$r['jenis'],$r['deskripsi'],$r['penanggung_jawab'],$r['tanggal_upload'],$r['file_path']]);
    }
    fclose($out);
    exit;
}

include 'header.php';
?>
<h2>Halaman Laporan</h2>
<div class="card mb-3"><div class="card-body">
  <h5>Laporan Data Alat Multimedia</h5>
  <p><a class="btn btn-sm btn-primary" href="laporan.php?export=alat">Export CSV</a> <button class="btn btn-sm btn-secondary" onclick="window.open('laporan.php?print=alat','_blank')">Export PDF / Print</button></p>

  <h5>Laporan Maintenance Alat</h5>
  <p><a class="btn btn-sm btn-primary" href="laporan.php?export=maintenance">Export CSV</a> <button class="btn btn-sm btn-secondary" onclick="window.open('laporan.php?print=maintenance','_blank')">Export PDF / Print</button></p>

  <h5>Laporan Permohonan Pembelian</h5>
  <p><a class="btn btn-sm btn-primary" href="laporan.php?export=pembelian">Export CSV</a> <button class="btn btn-sm btn-secondary" onclick="window.open('laporan.php?print=pembelian','_blank')">Export PDF / Print</button></p>

  <h5>Laporan Pengeluaran Alat Multimedia</h5>
  <p><a class="btn btn-sm btn-primary" href="laporan.php?export=pengeluaran">Export CSV</a> <button class="btn btn-sm btn-secondary" onclick="window.open('laporan.php?print=pengeluaran','_blank')">Export PDF / Print</button></p>

  <h5>Laporan Konten Multimedia</h5>
  <p><a class="btn btn-sm btn-primary" href="laporan.php?export=konten">Export CSV</a> <button class="btn btn-sm btn-secondary" onclick="window.open('laporan.php?print=konten','_blank')">Export PDF / Print</button></p>
</div></div>

<?php
// Printable views
if(isset($_GET['print'])){
    $type = $_GET['print'];
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Print Report</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="p-4">';
    echo '<h3>Laporan '.htmlspecialchars($type).'</h3>';
    if($type==='alat'){
        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Nama</th><th>Jenis</th><th>Kondisi</th><th>Lokasi</th><th>PJ</th><th>Tgl</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT * FROM tabel_alat"); while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id_alat'].'</td><td>'.htmlspecialchars($r['nama_alat']).'</td><td>'.htmlspecialchars($r['jenis']).'</td><td>'.$r['kondisi'].'</td><td>'.htmlspecialchars($r['lokasi']).'</td><td>'.htmlspecialchars($r['penanggung_jawab']).'</td><td>'.$r['tanggal_pembelian'].'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='maintenance'){
        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Alat</th><th>Jenis</th><th>Tanggal</th><th>Teknisi</th><th>Biaya</th><th>Status</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT m.*, a.nama_alat FROM tabel_maintenance m LEFT JOIN tabel_alat a ON m.id_alat=a.id_alat"); while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id_maintenance'].'</td><td>'.htmlspecialchars($r['nama_alat']).'</td><td>'.htmlspecialchars($r['jenis_maintenance']).'</td><td>'.$r['tanggal'].'</td><td>'.htmlspecialchars($r['teknisi']).'</td><td>'.number_format($r['biaya'],0,',','.').'</td><td>'.$r['status'].'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='pembelian'){
        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Nama</th><th>Alasan</th><th>Estimasi</th><th>Tgl</th><th>Status</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT * FROM tabel_pembelian"); while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id_pembelian'].'</td><td>'.htmlspecialchars($r['nama_alat']).'</td><td>'.htmlspecialchars($r['alasan']).'</td><td>'.number_format($r['estimasi_biaya'],0,',','.').'</td><td>'.$r['tanggal_permohonan'].'</td><td>'.$r['status'].'</td></tr>';
        echo '</tbody></table>';
    } elseif($type==='pengeluaran'){
        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Jenis</th><th>Nama Alat</th><th>Tanggal</th><th>Nominal</th><th>Keterangan</th></tr></thead><tbody>';
        $tot = 0; $q=mysqli_query($conn,"SELECT * FROM tabel_pengeluaran"); while($r=mysqli_fetch_assoc($q)){ $tot += $r['nominal']; echo '<tr><td>'.$r['id_pengeluaran'].'</td><td>'.$r['jenis_pengeluaran'].'</td><td>'.htmlspecialchars($r['nama_alat']).'</td><td>'.$r['tanggal'].'</td><td>'.number_format($r['nominal'],0,',','.').'</td><td>'.htmlspecialchars($r['keterangan']).'</td></tr>'; }
        echo '</tbody><tfoot><tr><th colspan="4">Total</th><th>'.number_format($tot,0,',','.').'</th><th></th></tr></tfoot></table>';
    } elseif($type==='konten'){
        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>Judul</th><th>Jenis</th><th>PJ</th><th>Tgl</th><th>File</th></tr></thead><tbody>';
        $q=mysqli_query($conn,"SELECT * FROM tabel_konten"); while($r=mysqli_fetch_assoc($q)) echo '<tr><td>'.$r['id_konten'].'</td><td>'.htmlspecialchars($r['judul']).'</td><td>'.$r['jenis'].'</td><td>'.htmlspecialchars($r['penanggung_jawab']).'</td><td>'.$r['tanggal_upload'].'</td><td>'.htmlspecialchars($r['file_path']).'</td></tr>';
        echo '</tbody></table>';
    }
    echo '<script>window.print();</script></body></html>';
    exit;
}

include 'footer.php';
?>