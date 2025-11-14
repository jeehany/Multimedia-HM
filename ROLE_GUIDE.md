# Role-Based Access Control Guide

## Sistem Role dan Akses

Aplikasi Pengelolaan dan Maintenance Alat Multimedia HM Official mengimplementasikan sistem role berbasis 3 tingkatan akses untuk mengelola hak akses pengguna terhadap berbagai modul.

---

## 1. Definisi Role

### Admin

- **Deskripsi**: Administrator dengan hak akses penuh
- **Fungsi**: Mengelola semua data aplikasi, membuat laporan, dan mengatur sistem
- **Hak Akses**:
  - ✅ Create (Buat) - Semua modul
  - ✅ Read (Baca) - Semua data
  - ✅ Update (Edit) - Semua data
  - ✅ Delete (Hapus) - Semua data
  - ✅ Print Laporan - Semua jenis laporan
  - ❌ Approve - Tidak memiliki tombol Approve (bukan tugas admin)

### Kepala (Head/Manager)

- **Deskripsi**: Kepala departemen dengan hak akses read-only + approval
- **Fungsi**: Memonitor data dan memberikan persetujuan atas permohonan/permintaan
- **Hak Akses**:
  - ❌ Create (Buat) - Tidak dapat membuat data baru
  - ✅ Read (Baca) - Dapat melihat semua data
  - ❌ Update (Edit) - Tidak dapat mengubah data
  - ❌ Delete (Hapus) - Tidak dapat menghapus data
  - ✅ Print Laporan - Dapat mencetak laporan
  - ✅ Approve Permohonan - Dapat menyetujui/menolak permohonan pembelian
  - 📝 Mode: **Read-Only dengan Approval Authority**

### Staff (Petugas)

- **Deskripsi**: Staf umum dengan hak akses CRUD lengkap
- **Fungsi**: Menginput, memelihara, dan mengelola data harian
- **Hak Akses**:
  - ✅ Create (Buat) - Semua modul
  - ✅ Read (Baca) - Semua data
  - ✅ Update (Edit) - Semua data
  - ✅ Delete (Hapus) - Semua data
  - ✅ Print Laporan - Semua jenis laporan
  - ❌ Approve - Tidak memiliki tombol Approve
  - 📝 Mode: **Full Edit Access**

---

## 2. Modul dan Hak Akses per Role

| Modul           |  Admin   |       Kepala       |  Staff   |
| --------------- | :------: | :----------------: | :------: |
| **Alat**        | CRUD ✅  |       Read 👁       | CRUD ✅  |
| **Maintenance** | CRUD ✅  |       Read 👁       | CRUD ✅  |
| **Pembelian**   | CRUD ✅  | Read + Approve 👁✅ | CRUD ✅  |
| **Pengeluaran** | CRUD ✅  |       Read 👁       | CRUD ✅  |
| **Konten**      | CRUD ✅  |       Read 👁       | CRUD ✅  |
| **Laporan**     | Print ✅ |      Print ✅      | Print ✅ |
| **Dashboard**   | View ✅  |      View ✅       | View ✅  |

---

## 3. Fitur Khusus: Approve Permohonan Pembelian

### Workflow Permohonan Pembelian

1. **Staff/Admin** membuat permohonan pembelian (status: `menunggu`)
2. **Kepala** melihat permohonan dalam status `menunggu`
3. **Kepala** dapat memilih:
   - ✅ **Setujui** → Status berubah menjadi `disetujui`
   - ❌ **Tolak** → Status berubah menjadi `ditolak`
4. Setelah di-approve/tolak, tombol Approve hilang dan status ditampilkan

### Status Permohonan

| Status      | Badge               | Artinya                                |
| ----------- | ------------------- | -------------------------------------- |
| `menunggu`  | 🟡 Warning (Yellow) | Menunggu persetujuan dari Kepala       |
| `disetujui` | 🟢 Success (Green)  | Permohonan telah disetujui oleh Kepala |
| `ditolak`   | 🔴 Danger (Red)     | Permohonan telah ditolak oleh Kepala   |

---

## 4. Implementasi Technical

### Helper Functions (config.php)

```php
is_admin()      // Check if user is admin
is_kepala()     // Check if user is kepala
is_staff()      // Check if user is staff
can_edit()      // Check if user can create/update/delete (admin or staff)
can_approve()   // Check if user can approve permohonan (kepala only)
```

### Conditional Rendering Pattern

**Menyembunyikan Form Input untuk Kepala:**

```php
<?php if(can_edit()): ?>
  <form><!-- Form untuk Admin dan Staff --></form>
<?php else: ?>
  <div class="alert">Mode Baca Saja (Read-Only)</div>
<?php endif; ?>
```

**Tombol Action Bersyarat:**

```php
<?php if(can_edit()): ?>
  <!-- Edit/Delete buttons untuk Admin dan Staff -->
  <a class="btn btn-primary" href="#edit">Edit</a>
  <a class="btn btn-danger" href="?delete=">Hapus</a>
<?php elseif(can_approve()): ?>
  <!-- Approve/Reject buttons untuk Kepala (di pembelian.php) -->
  <a class="btn btn-success" href="?approve=...">Setujui</a>
  <a class="btn btn-danger" href="?approve=...">Tolak</a>
<?php endif; ?>
```

---

## 5. Daftar User Sampel

Database disertakan dengan 3 user sampel untuk testing:

| Username | Password    | Role   | Nama          |
| -------- | ----------- | ------ | ------------- |
| `admin`  | `admin123`  | admin  | Administrator |
| `kepala` | `kepala123` | kepala | Kepala HM     |
| `staff`  | `staff123`  | staff  | Staff HM      |

**Catatan**: Password disimpan dengan hashing MD5 (untuk demo saja; gunakan bcrypt di production)

---

## 6. Testing Checklist

### Test Login & Role Detection

- [ ] Login sebagai Admin → Verifikasi akses CRUD ke semua modul
- [ ] Login sebagai Kepala → Verifikasi read-only mode di semua modul
- [ ] Login sebagai Staff → Verifikasi akses CRUD ke semua modul

### Test Admin Features

- [ ] Admin dapat membuat alat baru (Alat module)
- [ ] Admin dapat mengedit maintenance record
- [ ] Admin dapat membuat permohonan pembelian
- [ ] Admin dapat mencatat pengeluaran
- [ ] Admin dapat upload konten multimedia
- [ ] Admin dapat mencetak laporan
- [ ] Admin **tidak memiliki** tombol Approve di halaman pembelian

### Test Kepala Features

- [ ] Kepala melihat form input **disabled/hidden** di semua modul
- [ ] Kepala melihat alert "Mode Baca Saja (Read-Only)" di setiap halaman
- [ ] Kepala melihat tombol Edit/Delete **bersembunyi** di tabel
- [ ] Kepala melihat tombol Approve **HANYA** di halaman pembelian (permohonan status='menunggu')
- [ ] Kepala dapat klik "Setujui" → Status berubah `disetujui`, tombol hilang
- [ ] Kepala dapat klik "Tolak" → Status berubah `ditolak`, tombol hilang
- [ ] Kepala dapat mencetak laporan dari halaman laporan

### Test Staff Features

- [ ] Staff dapat membuat alat baru
- [ ] Staff dapat mengedit/menghapus alat yang sudah ada
- [ ] Staff dapat membuat maintenance record
- [ ] Staff dapat membuat permohonan pembelian
- [ ] Staff **tidak memiliki** tombol Approve di halaman pembelian
- [ ] Staff dapat mencetak laporan

### Test UI/UX

- [ ] Alert "Mode Baca Saja" muncul di halaman untuk user Kepala
- [ ] Status badges ditampilkan dengan warna yang benar:
  - Yellow untuk `menunggu`
  - Green untuk `disetujui`
  - Red untuk `ditolak`
- [ ] Semua menu module tetap terlihat di header navigation
- [ ] Tabel responsif di mobile view

---

## 7. Troubleshooting

### Issue: Kepala masih bisa edit data

**Solusi**: Pastikan helper function `can_edit()` di config.php sudah ditambahkan dengan benar:

```php
function can_edit() {
    return is_admin() || is_staff();
}
```

### Issue: Tombol Approve tidak muncul untuk Kepala

**Solusi**: Pastikan di pembelian.php ada kondisi untuk dapat_approve():

```php
elseif(can_approve()) {
    // Show Approve buttons
}
```

### Issue: Kepala masih bisa delete item

**Solusi**: Verifikasi bahwa DELETE handler dibungkus dalam `if(can_edit())` block

### Issue: Form masih bisa diisi oleh Kepala

**Solusi**: Pastikan form dibungkus dengan `<?php if(can_edit()): ?> ... <?php endif; ?>`

---

## 8. Update Database Role

Jika perlu menambah/mengubah role di masa depan:

1. Update `tabel_user` schema:

   ```sql
   ALTER TABLE tabel_user CHANGE COLUMN role role ENUM('admin', 'kepala', 'staff', 'newrole') DEFAULT 'staff';
   ```

2. Update helper functions di `config.php`:

   ```php
   function is_newrole() {
       return current_user() && current_user()['role'] === 'newrole';
   }
   ```

3. Update modul yang memerlukan logic baru

---

## 9. Backup dan Recovery

Untuk restore default role system:

1. Jalankan ulang `install.sql` untuk reset database
2. Atau gunakan SQL:
   ```sql
   UPDATE tabel_user SET role = 'admin' WHERE username = 'admin';
   UPDATE tabel_user SET role = 'kepala' WHERE username = 'kepala';
   UPDATE tabel_user SET role = 'staff' WHERE username = 'staff';
   ```

---

## 10. Log Approval (Optional Enhancement)

Untuk melacak siapa dan kapan suatu permohonan di-approve, bisa ditambahkan field di `tabel_pembelian`:

```sql
ALTER TABLE tabel_pembelian ADD COLUMN approved_by INT;
ALTER TABLE tabel_pembelian ADD COLUMN approved_at DATETIME;
ALTER TABLE tabel_pembelian ADD COLUMN approval_status ENUM('menunggu', 'disetujui', 'ditolak');
```

---

**Terakhir diupdate**: 2024
**Versi**: 1.0
**Kompatibilitas**: PHP 7.4+, MySQL 5.7+, MariaDB 10.3+
