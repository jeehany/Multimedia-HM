## TESTING CHECKLIST - Aplikasi Pengelolaan Alat Multimedia HM Official

### 1. Setup Database
- [ ] Buka phpMyAdmin atau MySQL command line
- [ ] Import file `install.sql` ke MySQL
- [ ] Verifikasi database `multimedia_hm` berhasil dibuat dengan 6 tabel
- [ ] Verifikasi data contoh sudah terisi (minimal 5 baris per tabel)

### 2. Login
- [ ] Akses http://localhost/multimedia-hm
- [ ] Login dengan admin / admin123
- [ ] Verifikasi redirect ke dashboard
- [ ] Logout dan login dengan staff / staff123
- [ ] Verifikasi session bekerja

### 3. Dashboard (index.php)
- [ ] Verifikasi summary cards menampilkan:
  - Total Alat
  - Alat Rusak (count where kondisi != 'baik')
  - Permohonan (count where status = 'menunggu')
  - Konten
- [ ] Verifikasi 2 chart ada dan menampilkan data:
  - Doughnut chart: Distribusi Kondisi Alat
  - Bar chart: Pengeluaran per Bulan (6 bulan terakhir)

### 4. CRUD Pages
**Alat (alat.php)**
- [ ] Verifikasi form input dan tombol Simpan bekerja
- [ ] Verifikasi data baru muncul di tabel
- [ ] Verifikasi tombol Edit bisa mengisi form
- [ ] Verifikasi tombol Hapus menghapus data

**Maintenance, Pembelian, Pengeluaran, Konten**
- [ ] Lakukan test CRUD untuk masing-masing halaman
- [ ] Verifikasi file upload bekerja untuk konten.php
- [ ] Verifikasi file disimpan ke folder uploads/

### 5. Laporan (REVISI - Main Testing)

#### 5.1 Dashboard Laporan (laporan.php tanpa report param)
- [ ] Akses http://localhost/multimedia-hm/laporan.php
- [ ] Verifikasi 5 card laporan muncul:
  1. Data Alat Multimedia
  2. Maintenance Alat
  3. Permohonan Pembelian
  4. Pengeluaran Alat Multimedia
  5. Konten Multimedia
- [ ] Setiap card memiliki 3 tombol: "Lihat Laporan", "Export CSV", "Print / PDF"

#### 5.2 Laporan Data Alat (laporan.php?report=alat)
**Visual:**
- [ ] 4 Summary cards muncul (Total, Baik, Rusak, Perlu Perbaikan) dengan warna berbeda
- [ ] 2 Chart muncul:
  - Doughnut: Distribusi Kondisi Alat
  - Bar (horizontal): Distribusi Alat Per Lokasi
- [ ] Tabel Detail Data Alat muncul dengan kolom: ID, Nama, Jenis, Kondisi, Lokasi, PJ, Tgl Pembelian

**Export CSV (laporan.php?export=alat):**
- [ ] File CSV download dengan nama laporan_alat_YYYY-MM-DD.csv
- [ ] Buka di Excel, verifikasi:
  - Baris pertama: "LAPORAN DATA ALAT MULTIMEDIA"
  - Baris kedua: "Tanggal Export" dan timestamp
  - Bagian RINGKASAN: Total Alat, Kondisi Baik, Kondisi Rusak, Perlu Perbaikan
  - Bagian REKAP PER LOKASI: List lokasi dan jumlah alat per lokasi
  - Bagian DETAIL DATA ALAT: Tabel lengkap data alat

**Print / PDF (laporan.php?report=alat&print=1):**
- [ ] Tab baru terbuka dengan halaman printable
- [ ] Summary cards terlihat jelas di atas (Total, Baik, Rusak, Perlu Perbaikan)
- [ ] Rekap per lokasi terlihat
- [ ] Tabel detail terlihat
- [ ] Print halaman (File > Print atau Ctrl+P)
- [ ] Save as PDF dan verifikasi

#### 5.3 Laporan Maintenance (laporan.php?report=maintenance)
**Visual:**
- [ ] 1 Summary card muncul: "Total Biaya Maintenance" (angka hasil SUM(biaya))
- [ ] Tabel muncul dengan kolom: ID, Alat, Jenis, Tanggal, Teknisi, Biaya, Status
- [ ] Footer tabel menampilkan "Total Biaya" dengan nilai yang sama dengan card

**Export CSV:**
- [ ] CSV download, verifikasi ringkasan "Total Biaya Maintenance" ada

**Print / PDF:**
- [ ] Halaman printable dengan summary "Total Biaya Maintenance" di atas

#### 5.4 Laporan Permohonan Pembelian (laporan.php?report=pembelian)
**Visual:**
- [ ] 1 Summary card muncul: "Total Permohonan" (jumlah baris)
- [ ] Tabel muncul dengan status badges (warna berbeda per status: menunggu, disetujui, ditolak)
- [ ] Footer tabel menampilkan "Total Permohonan"

**Export CSV:**
- [ ] CSV download dengan ringkasan "Total Permohonan"

**Print / PDF:**
- [ ] Summary "Total Permohonan" terlihat di atas

#### 5.5 Laporan Pengeluaran (laporan.php?report=pengeluaran)
**Visual:**
- [ ] Form filter bulan (input type="month") ada di atas
- [ ] 1 Summary card: "Total Pengeluaran" (hasil SUM(nominal) dari filter)
- [ ] Tabel muncul dengan kolom: ID, Jenis, Nama Alat, Tanggal, Nominal, Keterangan
- [ ] Footer tabel menampilkan "Total Pengeluaran"
- [ ] Test filter:
  - Pilih bulan tertentu
  - Verifikasi tabel hanya menampilkan data bulan tersebut
  - Verifikasi Total Pengeluaran card berganti sesuai filter
  - Klik "Reset" untuk kembali ke data semua bulan

**Export CSV:**
- [ ] CSV download dengan ringkasan "Total Pengeluaran"

**Print / PDF:**
- [ ] Halaman printable dengan summary "Total Pengeluaran"

#### 5.6 Laporan Konten Multimedia (laporan.php?report=konten)
**Visual:**
- [ ] Tabel muncul dengan kolom: ID, Judul, Jenis, Deskripsi, PJ, Tanggal Upload, File

**Export CSV:**
- [ ] CSV download (standar tanpa perubahan)

**Print / PDF:**
- [ ] Halaman printable dengan tabel konten

### 6. Data Consistency Check
- [ ] Verifikasi Summary cards nilai sama antara view, CSV export, dan printable view
- [ ] Verifikasi Total Pengeluaran konsisten saat filter diubah
- [ ] Verifikasi Chart.js menampilkan data dari database (bukan hardcoded)

### 7. Error Handling
- [ ] Coba akses laporan tanpa login -> redirect ke login.php ✓ (sudah ada require_login())
- [ ] Coba export dengan database kosong -> CSV tetap generate (dengan data kosong)
- [ ] Periksa browser console untuk JavaScript errors

### 8. Browser Compatibility
- [ ] Test di Chrome
- [ ] Test di Firefox
- [ ] Test Print to PDF di masing-masing browser

### 9. Data Verification Examples
Gunakan data dari install.sql untuk verifikasi:
- **tabel_alat**: 5 records
  - 1 baik, 1 rusak ringan, 1 rusak berat, 1 baik, 1 rusak berat
  - Lokasi: Studio, Ruang Audio, Gudang, Panggung, Gudang
- **tabel_maintenance**: 5 records, total biaya = 700,000
- **tabel_pembelian**: 5 records (2 menunggu, 1 disetujui, 1 ditolak, 1 menunggu)
- **tabel_pengeluaran**: 5 records, total = 2,300,000
- **tabel_konten**: 5 records (berbagai jenis: foto, video, audio, desain)

### 10. Performance Check
- [ ] Load laporan dengan banyak data (>1000 records) - tidak lag
- [ ] CSV export besar tidak timeout
- [ ] Verifikasi query tidak melakukan N+1 problem

## NOTES
- Semua revisi ada di file `laporan.php`
- Helper functions dimulai dari baris ~5
- Export handler mulai dari baris ~60
- Report view mulai dari baris ~170
- Print handler mulai dari baris ~550
- Tidak ada perubahan di database schema
- Tidak ada perubahan di CRUD pages atau dashboard
