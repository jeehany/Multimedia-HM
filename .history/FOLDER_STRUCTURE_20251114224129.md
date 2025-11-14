# 📂 Struktur Folder Aplikasi HM Multimedia - Perapian Terbaru

## 📋 Daftar Isi
1. [Struktur Folder](#struktur-folder)
2. [Penjelasan Setiap Folder](#penjelasan-setiap-folder)
3. [File Global (Root)](#file-global-root)
4. [Navigasi & Routing](#navigasi--routing)
5. [Perubahan yang Dilakukan](#perubahan-yang-dilakukan)

---

## 📁 Struktur Folder

```
multimedia-hm/ (root)
│
├─── 📄 config.php                          ← Database connection & role helpers
├─── 📄 header.php                          ← Global navbar + sidebar (updated dengan path baru)
├─── 📄 footer.php                          ← Global footer + auto-filter JS
├─── 📄 index.php                           ← Dashboard halaman utama
├─── 📄 login.php                           ← Halaman login
├─── 📄 logout.php                          ← Handler logout
├─── 📄 install.sql                         ← Database schema & sample data
│
├─── 📁 assets/                              ← Static files
│    ├─── css/
│    ├─── js/
│    ├─── images/
│    └─── fonts/
│
├─── 📁 uploads/                             ← User-uploaded files (konten multimedia)
│    └─── [uploaded files...]
│
├─── 📁 views/                               ← Presentation layer
│    └─── 📁 pages/                          ← Semua halaman CRUD & list
│         ├─── 📄 alat.php                   ← List alat (dengan filter/search)
│         ├─── 📄 alat_add.php               ← Form tambah alat
│         ├─── 📄 alat_edit.php              ← Form edit alat
│         │
│         ├─── 📄 maintenance.php            ← List maintenance
│         ├─── 📄 maintenance_add.php        ← Form tambah maintenance
│         ├─── 📄 maintenance_edit.php       ← Form edit maintenance
│         │
│         ├─── 📄 pembelian.php              ← List permohonan pembelian
│         ├─── 📄 pembelian_add.php          ← Form tambah pembelian
│         ├─── 📄 pembelian_edit.php         ← Form edit pembelian
│         │
│         ├─── 📄 pengeluaran.php            ← List pengeluaran
│         ├─── 📄 pengeluaran_add.php        ← Form tambah pengeluaran
│         ├─── 📄 pengeluaran_edit.php       ← Form edit pengeluaran
│         │
│         ├─── 📄 konten.php                 ← Galeri konten multimedia
│         ├─── 📄 konten_add.php             ← Form tambah konten
│         └─── 📄 konten_edit.php            ← Form edit konten
│
├─── 📁 reports/                             ← Laporan & export
│    └─── 📄 laporan.php                     ← Dashboard laporan + export CSV & print
│
├─── 📁 controllers/                         ← Business logic handlers (siap untuk ekspansi)
│    └─── [kosong - untuk handler CRUD di masa depan]
│
├─── 📁 models/                              ← Data models & queries (siap untuk ekspansi)
│    └─── [kosong - untuk query builders di masa depan]
│
├─── 📁 .history/                            ← VS Code history (auto-generated)
│
└─── 📄 [doc files...]                       ← Dokumentasi project
     ├─── README.md
     ├─── ROLE_GUIDE.md
     └─── [files lainnya...]
```

---

## 🎯 Penjelasan Setiap Folder

### 📍 Root (`multimedia-hm/`)
Hanya berisi **file-file global** yang menjalankan aplikasi:
- **config.php** – Database connection, role helpers (is_admin, is_kepala, can_edit, can_approve)
- **header.php** – Navbar responsive + sidebar (diinclude di semua halaman)
- **footer.php** – Penutup HTML + auto-filter JS script
- **index.php** – Dashboard halaman depan (setelah login)
- **login.php** – Halaman login
- **logout.php** – Handler session logout
- **install.sql** – Database schema dengan 5 tabel utama

### 📁 assets/
Menyimpan semua file statis:
- `css/` – Bootstrap 5, custom CSS
- `js/` – Bootstrap bundle, Chart.js
- `images/` – Logo, ikon custom
- Diakses dari seluruh aplikasi via CDN atau path relatif

### 📁 uploads/
Folder penyimpanan file upload dari **Manajemen Konten**:
- File multimedia (foto, video, audio, desain) disimpan di sini
- Path file disimpan di database tabel `tabel_konten`

### 📁 views/pages/
**SEMUA halaman CRUD list/add/edit** diorganisir per modul:
- `alat.php, alat_add.php, alat_edit.php` – Manajemen Alat Multimedia
- `maintenance.php, maintenance_add.php, maintenance_edit.php` – Manajemen Maintenance
- `pembelian.php, pembelian_add.php, pembelian_edit.php` – Manajemen Pembelian
- `pengeluaran.php, pengeluaran_add.php, pengeluaran_edit.php` – Manajemen Pengeluaran
- `konten.php, konten_add.php, konten_edit.php` – Manajemen Konten Multimedia

**Semua file:**
- Include config.php dengan path: `__DIR__ . '/../../config.php'`
- Include header.php dengan path: `__DIR__ . '/../../header.php'`
- Include footer.php dengan path: `__DIR__ . '/../../footer.php'`
- Fitur search/filter tersedia (kecuali add/edit)

### 📁 reports/
Berisi **halaman laporan & export**:
- `laporan.php` – Dashboard dengan 5 tipe laporan (alat, maintenance, pembelian, pengeluaran, konten)
- Fitur: View tabel, Export CSV, Print PDF, Chart.js visualisasi

### 📁 controllers/ & models/
**Folder kosong** siap untuk ekspansi di masa depan:
- **controllers/** – untuk memisahkan business logic dari presentation
- **models/** – untuk centralize database queries & ORM-like layer

---

## 🧭 Navigasi & Routing

### Dari `header.php` (Navigation Sidebar)
```html
Dashboard                → href="/index.php"
Data Alat               → href="/views/pages/alat.php"
Maintenance             → href="/views/pages/maintenance.php"
Permohonan Pembelian    → href="/views/pages/pembelian.php"
Pengeluaran             → href="/views/pages/pengeluaran.php"
Manajemen Konten        → href="/views/pages/konten.php"
Laporan                 → href="/reports/laporan.php"
```

### Internal Links (dalam halaman list)
- **Edit**: `href="alat_edit.php?id=X"` (sama folder)
- **Add**: `href="alat_add.php"` (sama folder)
- **Back**: `href="alat.php"` (sama folder)
- **Delete**: `href="?delete=X"` (self-referencing)

### Form Redirect Setelah POST
- Add/Edit: `header('Location: alat.php'); exit;` (kembali ke list)

---

## 🔧 Perubahan yang Dilakukan

### Sebelum Perapian
```
root/
├─ config.php
├─ header.php
├─ footer.php
├─ alat.php
├─ alat_add.php
├─ alat_edit.php
├─ maintenance.php
├─ [10 file lainnya...]
├─ laporan.php
└─ [assets, uploads, ...]
```
**Masalah:** Root berantakan dengan 15 file halaman bercampur dengan file global.

### Sesudah Perapian ✅
```
root/
├─ config.php           (global)
├─ header.php           (global, updated)
├─ footer.php           (global)
├─ index.php            (global)
├─ login.php, logout.php (global)
├─ install.sql          (global)
│
├─ views/pages/         (SEMUA CRUD pages)
│  ├─ alat.php, alat_add.php, alat_edit.php
│  ├─ maintenance.php, maintenance_add.php, maintenance_edit.php
│  ├─ pembelian.php, pembelian_add.php, pembelian_edit.php
│  ├─ pengeluaran.php, pengeluaran_add.php, pengeluaran_edit.php
│  └─ konten.php, konten_add.php, konten_edit.php
│
├─ reports/             (Laporan)
│  └─ laporan.php
│
├─ controllers/         (Siap ekspansi)
├─ models/              (Siap ekspansi)
├─ assets/
├─ uploads/
└─ [docs...]
```
**Manfaat:**
- ✅ Root hanya 7-8 file global (bersih & mudah navigasi)
- ✅ Semua CRUD page terorganisir di `views/pages/`
- ✅ Laporan terisolasi di `reports/`
- ✅ Folder `controllers/` dan `models/` siap untuk refactor lanjutan
- ✅ Path file sudah terupdate (include relative path dengan `__DIR__`)
- ✅ Navigasi di `header.php` sudah terupdate

### File yang Diubah
1. **header.php** – Tambah base URL logic + update sidebar links ke `views/pages/` & `reports/`
2. **views/pages/*.php** – Ubah require path dari `require_once 'config.php'` → `require_once __DIR__ . '/../../config.php'` (15 file)
3. **reports/laporan.php** – Ubah require path untuk config/header/footer

### Fitur yang TIDAK Berubah
- ✅ Semua logic CRUD tetap sama
- ✅ Database schema tetap sama
- ✅ Role-based access tetap sama
- ✅ Filter/search tetap berfungsi
- ✅ Approval workflow di pembelian tetap
- ✅ File upload konten tetap
- ✅ Laporan & export CSV tetap

---

## 📖 Catatan untuk Developer

### Jika ingin menambah halaman baru:
1. Buat file di `views/pages/[nama].php`
2. Include `__DIR__ . '/../../config.php'` dan `__DIR__ . '/../../header.php'`
3. Update sidebar di `header.php` dengan link baru

### Jika ingin refactor ke MVC penuh:
1. Pindahkan database queries ke `models/[module].php`
2. Pindahkan handler form POST ke `controllers/[module]_controller.php`
3. Halaman di `views/pages/` hanya menampilkan form & tabel
4. Include controller di atas view untuk process data

### Struktur MVC yang disarankan (ekspansi di masa depan):
```
controllers/
├─ AlatController.php
├─ MaintenanceController.php
├─ PembelianController.php
└─ [...]

models/
├─ Alat.php
├─ Maintenance.php
├─ Pembelian.php
└─ [...]
```

---

**Created:** Nov 2025  
**Status:** ✅ Struktur folder rapi & terorganisir  
**Next Step:** Continuous refactor ke MVC pattern (opsional)
