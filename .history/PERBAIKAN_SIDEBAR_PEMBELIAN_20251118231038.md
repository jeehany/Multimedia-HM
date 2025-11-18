# 🔧 PERBAIKAN - SIDEBAR LAPORAN & PEMBELIAN AKSES

**Tanggal:** November 18, 2025  
**Status:** ✅ **SELESAI**

---

## 🐛 Masalah yang Diperbaiki

### 1. ❌ Sidebar Tidak Bisa Diakses di Halaman Laporan
**Penyebab:** File `laporan.php` berada di folder `/reports/`, tapi `header.php` hanya menghitung base path untuk file di folder `/views/pages/`

**Solusi:** Update `header.php` untuk handle kedua folder:
```php
// BEFORE (hanya handle /views/pages/)
if(preg_match('#/views/pages/#', $script)){
  $base = preg_replace('#/views/pages/.*$#','', $script);
} else {
  $base = dirname($script);
}

// AFTER (handle /views/pages/ dan /reports/)
if(preg_match('#/views/pages/#', $script)){
  $base = preg_replace('#/views/pages/.*$#','', $script);
} elseif(preg_match('#/reports/#', $script)){
  $base = preg_replace('#/reports/.*$#','', $script);
} else {
  $base = dirname($script);
}
```

**Hasil:** 
- ✅ Sidebar sekarang visible di laporan.php
- ✅ Semua navigation links bekerja (Dashboard, Data Alat, Maintenance, Pembelian, Pengeluaran, Konten, Laporan)

---

### 2. ❌ Halaman Pembelian Tidak Bisa Diakses
**Penyebab:** `pembelian.php` memiliki redirect di awal yang hanya allow role `can_approve()` (Kepala saja)

**Solusi:** Ubah akses model menjadi:
- **Kepala** (`can_approve()`): Full access (create, edit, delete, **approve**, reject)
- **Admin** (`can_edit()`): Limited access (create, edit, delete, **tapi NO approve/reject**)
- **Other roles**: Read-only (view only, no actions)

**Perubahan:**

| Aksi | Sebelum | Sesudah |
|------|---------|--------|
| View halaman | ❌ Hanya Kepala | ✅ Semua role |
| Tambah | ❌ Hanya Kepala | ✅ Kepala & Admin |
| Edit | ❌ Hanya Kepala | ✅ Kepala & Admin |
| Delete | ❌ Hanya Kepala | ✅ Kepala & Admin |
| Approve/Reject | ❌ Hanya Kepala | ✅ **Hanya Kepala** |

**Files yang diubah:**
```
views/pages/pembelian.php
views/pages/pembelian_add.php
views/pages/pembelian_edit.php
```

---

## 📝 Detail Perubahan

### header.php
```php
// Added: elseif untuk handle /reports/ folder
elseif(preg_match('#/reports/#', $script)){
  $base = preg_replace('#/reports/.*$#','', $script);
}
```

### pembelian.php
**Struktur conditional:**
```
if(can_approve()) 
  → Kepala: lihat form filter + tombol Tambah, Edit, Delete, Setujui, Tolak
elseif(can_edit())
  → Admin: lihat form filter + tombol Tambah, Edit, Delete (NO Setujui/Tolak)
else
  → Other: read-only mode + form filter (no action buttons)
```

### pembelian_add.php
```php
// BEFORE: if(!can_approve())
// AFTER: if(!can_approve() && !can_edit())
```

### pembelian_edit.php
```php
// BEFORE: if(!can_approve())
// AFTER: if(!can_approve() && !can_edit())
```

---

## ✅ Testing Checklist

### Laporan Page
- [ ] Buka `/reports/laporan.php`
- [ ] Sidebar visible & tidak tersembunyi
- [ ] Klik menu di sidebar (Dashboard, Data Alat, dll) → navigate OK
- [ ] Print / PDF button bekerja (buka popup baru)
- [ ] Export CSV button bekerja

### Pembelian Page
- [ ] Login sebagai **Kepala** → akses `/views/pages/pembelian.php` ✅
- [ ] Login sebagai **Admin** → akses `/views/pages/pembelian.php` ✅
- [ ] Login sebagai **Other role** → akses `/views/pages/pembelian.php` (read-only) ✅

### Pembelian Actions - Kepala
- [ ] Tombol "Tambah" visible ✅
- [ ] Tombol "Edit" visible ✅
- [ ] Tombol "Hapus" visible ✅
- [ ] Tombol "Setujui" & "Tolak" visible (hanya untuk status menunggu) ✅

### Pembelian Actions - Admin
- [ ] Tombol "Tambah" visible ✅
- [ ] Tombol "Edit" visible ✅
- [ ] Tombol "Hapus" visible ✅
- [ ] Tombol "Setujui" & "Tolak" **NOT visible** ✅
- [ ] Alert "Mode Baca Saja" tidak ditampilkan ✅

### Pembelian Actions - Other
- [ ] Form filter visible tapi disabled/read-only ✅
- [ ] Alert "Mode Baca Saja" ditampilkan ✅
- [ ] Tidak ada action buttons ✅

### Date Filter (Pembelian)
- [ ] Filter tanggal awal/akhir bekerja ✅
- [ ] Auto-filter on change ✅

---

## 📊 Role Matrix (Updated)

| Feature | Admin | Kepala | Other |
|---------|-------|--------|-------|
| View Pembelian | ✅ | ✅ | ✅ |
| Buat Pembelian | ✅ | ✅ | ❌ |
| Edit Pembelian | ✅ | ✅ | ❌ |
| Hapus Pembelian | ✅ | ✅ | ❌ |
| Approve Pembelian | ❌ | ✅ | ❌ |
| Reject Pembelian | ❌ | ✅ | ❌ |

---

## 🚀 Hasil Final

✅ **Sidebar di laporan accessible** - Semua navigation links bekerja dengan base path yang benar

✅ **Pembelian accessible untuk semua role** - Tapi dengan permission yang berbeda per role

✅ **Approve/Reject hanya Kepala** - Admin dan role lain tidak bisa approve/reject

✅ **UI consistent** - Tombol tampil/hilang sesuai role permission

---

**Aplikasi siap untuk testing!** 🎉
