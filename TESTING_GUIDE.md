# 🧪 TESTING GUIDE - Revisi Aplikasi

**Tanggal:** November 18, 2025

---

## 📋 Quick Testing Steps

Ikuti langkah-langkah di bawah untuk memverifikasi setiap revisi.

---

## 1️⃣ Test Hak Akses Pembelian (Role-based)

### Test dengan Role Admin

```
1. Login sebagai Admin (username: admin, password: admin)
2. Klik menu "Permohonan Pembelian" di sidebar
3. Expected: Redirect ke halaman utama (/)
4. Reason: Admin TIDAK punya akses, hanya Kepala
```

### Test dengan Role Kepala

```
1. Login sebagai Kepala (username: kepala, password: kepala)
2. Klik menu "Permohonan Pembelian" di sidebar
3. Expected: Halaman pembelian.php loaded OK, tampil list permohonan
4. Button "Tambah" visible di kanan atas
```

### Test Membuat Permohonan (Kepala only)

```
1. Sebagai Kepala, klik "Tambah +" di halaman permohonan
2. Expected: Form add dengan fields:
   - Nama Alat
   - Estimasi Biaya
   - Tanggal Permohonan
   - Alasan
   (TIDAK ADA field Status)
3. Isi form, klik "Simpan"
4. Expected: Permohonan dibuat, status otomatis "menunggu"
5. Redirect ke list permohonan
```

### Test Edit Permohonan (Kepala only)

```
1. Sebagai Kepala, di list klik tombol "Edit" di row permohonan
2. Expected: Form edit TANPA field Status
3. Hanya bisa edit: Nama, Estimasi, Tanggal, Alasan
4. Klik "Update", expected: Perubahan disimpan
```

### Test Approve/Reject (Kepala only)

```
1. Sebagai Kepala, buka list permohonan
2. Cari permohonan dengan status "menunggu"
3. Di action column, expected: 2 tombol:
   - Tombol hijau "Setujui" (✓)
   - Tombol kuning "Tolak" (×)
4. Klik "Setujui" → status berubah jadi "disetujui"
5. Coba permohonan "disetujui" → tombol approve/reject hilang, tampil badge "disetujui"
```

---

## 2️⃣ Test Filter Tanggal pada Pembelian

### Test Filter Tanggal Awal

```
1. Buka halaman "Permohonan Pembelian" sebagai Kepala
2. Di filter form, isi field "Tanggal Awal" = 2025-11-01
3. Expected: Tabel filter, tampil hanya permohonan >= 2025-11-01
4. Kosongkan field → tabel kembali tampil semua
```

### Test Filter Tanggal Akhir

```
1. Isi field "Tanggal Akhir" = 2025-11-15
2. Expected: Tabel filter, tampil hanya permohonan <= 2025-11-15
```

### Test Filter Rentang (Between)

```
1. Isi "Tanggal Awal" = 2025-11-01
2. Isi "Tanggal Akhir" = 2025-11-15
3. Expected: Tabel filter, tampil hanya permohonan BETWEEN 2025-11-01 AND 2025-11-15
4. Kombinasikan dengan filter Status & Search untuk test multi-filter
```

### Test Auto-Submit

```
1. Ubah salah satu filter (Search, Tanggal, Status)
2. Expected: Tabel auto-submit & refresh tanpa klik tombol
3. Delay ~350ms for text input, immediate for date/select
```

---

## 3️⃣ Test Tampilan Foto Konten

### Test Gambar

```
1. Login sebagai Admin
2. Buka "Manajemen Konten Multimedia"
3. Klik "Tambah +" → upload file gambar (jpg, png, gif)
4. Expected: Di galeri, tampil thumbnail gambar preview kecil
5. Image dimensions: 160x160px, object-fit: cover
```

### Test Video

```
1. Upload file video (mp4, webm, avi)
2. Expected: Di galeri, tampil placeholder ikon video (🎬)
3. Placeholder ukuran sama 160x160px
4. Title/hover: "Video: [filename]"
```

### Test Audio

```
1. Upload file audio (mp3, wav, ogg)
2. Expected: Di galeri, tampil placeholder ikon audio (🔊)
3. Ukuran 160x160px
4. Title/hover: "Audio: [filename]"
```

### Test File Tidak Ditemukan

```
1. Buka konten yang file_path-nya tidak exist/deleted
2. Expected: Tampil placeholder generik (📄)
3. Warna abu-abu (#666666)
```

### Test Filter Konten

```
1. Filter by jenis (Foto, Video, Audio, Desain)
2. Expected: Tabel filter, tampil thumbnail sesuai jenis
```

---

## 4️⃣ Test Sidebar di Laporan

### Test Sidebar Visible

```
1. Login sebagai Admin/Kepala
2. Buka Laporan → klik menu "Laporan"
3. Expected: Sidebar tetap visible di kiri
4. Navigasi tetap bisa diklik
```

### Test Navigation

```
1. Di halaman laporan, klik menu "Data Alat" di sidebar
2. Expected: Navigate ke /views/pages/alat.php OK
3. Klik "Laporan" lagi → back ke laporan
```

### Test Print View

```
1. Di halaman laporan (index), klik "Print / PDF"
2. Expected: Buka tab/window baru dengan laporan
3. Tab baru printable, sidebar TIDAK ada (OK)
4. Klik "Window.print()" di browser → print OK
```

---

## 5️⃣ Test Standardisasi Tombol Tambah

### Test Button Position (All CRUD Pages)

```
Halaman: Data Alat
1. Buka /views/pages/alat.php
2. Expected: Tombol "Tambah +" di kanan atas form filter
3. Position: column col-md-2, align-self-end

Halaman: Maintenance
1. Buka /views/pages/maintenance.php
2. Expected: Tombol "Tambah +" di kanan atas
3. Position: col-md-2, align-self-end

Halaman: Permohonan Pembelian
1. Buka /views/pages/pembelian.php
2. Expected: Tombol "Tambah +" di kanan atas
3. Position: col-md-1, align-self-end (narrow karena banyak filter)

Halaman: Pengeluaran
1. Buka /views/pages/pengeluaran.php
2. Expected: Tombol "Tambah +" di kanan atas
3. Position: col-md-2, align-self-end

Halaman: Konten
1. Buka /views/pages/konten.php
2. Expected: Tombol "Tambah +" di kanan atas
3. Position: col-md-2, align-self-end
```

### Test Button Style Consistency

```
Setiap halaman:
1. Button class: "btn btn-success"
2. Icon: "<i class="fa fa-plus"></i>"
3. Text: "Tambah"
4. Link href: "[page]_add.php"
5. Only visible jika current user punya permission
```

---

## 🎯 Integration Testing

### Test Flow Lengkap: Kepala Membuat & Approve Permohonan

```
1. Login sebagai Kepala
2. Buka "Permohonan Pembelian"
3. Klik "Tambah +" → isi form → simpan
4. Permohonan created dengan status "menunggu"
5. Di list, klik "Setujui" → status jadi "disetujui"
6. Filter by date awal/akhir → permohonan tetap visible
7. Back ke sidebar "Laporan" → navigasi OK
8. Test print laporan → OK
```

### Test Flow Admin Tidak Bisa Akses Pembelian

```
1. Login sebagai Admin
2. Direct URL: /views/pages/pembelian.php
3. Expected: Redirect ke /
4. Admin bisa akses Data Alat, Maintenance, Pengeluaran, Konten OK
```

### Test Konten Foto di Admin

```
1. Login sebagai Admin
2. Buka "Manajemen Konten"
3. Upload gambar, video, audio
4. Verify thumbnail rendering sesuai type
5. Delete konten → verify file deleted juga
```

---

## ✅ Final Checklist

- [ ] Pembelian: hanya Kepala punya akses ✓
- [ ] Pembelian add: status default "menunggu" ✓
- [ ] Pembelian edit: tidak ada field status ✓
- [ ] Pembelian: tombol Setujui/Tolak hanya untuk menunggu ✓
- [ ] Pembelian filter: date range working ✓
- [ ] Konten: gambar tampil sebagai thumbnail ✓
- [ ] Konten: video/audio tampil placeholder ✓
- [ ] Laporan: sidebar visible & interactive ✓
- [ ] Semua CRUD: button "Tambah" di kanan atas konsisten ✓
- [ ] All pages: no syntax/error ✓

---

## 📞 Troubleshooting

### Masalah: Admin bisa akses pembelian

**Solusi:** Clear browser cache, pastikan `can_approve()` check di pembelian.php

### Masalah: Foto konten tidak tampil

**Solusi:** Verify file_path di database, check file exists di /uploads/

### Masalah: Sidebar hilang di laporan

**Solusi:** Reload page, verify header.php include OK

### Masalah: Button "Tambah" posisi tidak konsisten

**Solusi:** Inspect HTML, verify col-md-\* class dan align-self-end ada

---

**Testing Status:** ✅ Ready for QA
