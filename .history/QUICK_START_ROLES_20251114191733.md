# Quick Start: Role-Based Access Control

## 🚀 Quick Setup

### 1. Database Setup
```bash
# Import the updated schema
mysql -u username -p database < install.sql
```

### 2. Test Login Credentials

| User | Password | Role | Access Level |
|------|----------|------|--------------|
| `admin` | `admin123` | Admin | Full CRUD + Reports |
| `kepala` | `kepala123` | Kepala | Read-Only + Approve |
| `staff` | `staff123` | Staff | Full CRUD |

---

## 📊 Role Permissions at a Glance

### Admin
- ✅ Can CREATE, READ, UPDATE, DELETE all data
- ✅ Can print reports
- ❌ No Approve button

### Kepala (Manager)
- ❌ Cannot CREATE, UPDATE, DELETE data (Read-only)
- ✅ Can READ all data
- ✅ Can print reports
- ✅ **Can APPROVE permohonan pembelian** (Purchase requests)

### Staff
- ✅ Can CREATE, READ, UPDATE, DELETE data
- ✅ Can print reports
- ❌ No Approve button

---

## 🔑 Key Features

### 1. Read-Only Mode for Kepala
When Kepala logs in:
- Form inputs are **hidden**
- Edit/Delete buttons are **hidden**
- Alert "Mode Baca Saja (Read-Only)" appears
- All modules remain **visible but read-only**

### 2. Approval Workflow (pembelian.php)
1. Staff/Admin creates permohonan → Status: `menunggu` ⏳
2. Kepala sees it and clicks "Setujui" or "Tolak" button
3. Status updates to `disetujui` ✅ or `ditolak` ❌
4. Button disappears, status badge shows permanent state

### 3. Status Badges
- 🟡 **menunggu** (Yellow) - Pending approval
- 🟢 **disetujui** (Green) - Approved
- 🔴 **ditolak** (Red) - Rejected

---

## 🛠️ Implementation Details

### Helper Functions (config.php)
```php
can_edit()      // True for Admin & Staff
can_approve()   // True for Kepala only
is_admin()      // True for Admin only
is_kepala()     // True for Kepala only
is_staff()      // True for Staff only
```

### Usage Pattern
```php
// Protect form submission
if(can_edit()) {
    // Handle POST/DELETE
}

// Conditionally display form
<?php if(can_edit()): ?>
    <form>...</form>
<?php else: ?>
    <div class="alert">Read-Only Mode</div>
<?php endif; ?>

// Conditional buttons
if(can_edit()) { echo "Edit/Delete buttons"; }
elseif(can_approve()) { echo "Approve/Reject buttons"; }
```

---

## ✅ Testing Checklist

### Admin User
- [ ] Login as admin / admin123
- [ ] Create new item in Alat module
- [ ] Edit existing item
- [ ] Delete item
- [ ] Print report
- [ ] Verify NO "Approve" button on pembelian page

### Kepala User
- [ ] Login as kepala / kepala123
- [ ] Verify form is hidden/disabled
- [ ] Verify "Mode Baca Saja" alert appears
- [ ] Verify Edit/Delete buttons are hidden
- [ ] Go to pembelian page
- [ ] Verify "Setujui" and "Tolak" buttons appear
- [ ] Click "Setujui" on pending permohonan
- [ ] Verify status changes to "disetujui" (green badge)
- [ ] Click "Tolak" on another permohonan
- [ ] Verify status changes to "ditolak" (red badge)
- [ ] Print report
- [ ] Verify can view all data but cannot modify

### Staff User
- [ ] Login as staff / staff123
- [ ] Create new item
- [ ] Edit existing item
- [ ] Delete item
- [ ] Print report
- [ ] Verify NO "Approve" button on pembelian page

---

## 📁 Modified Files

| File | Changes |
|------|---------|
| `install.sql` | Added "kepala" role, new sample user |
| `config.php` | Added 5 helper functions |
| `alat.php` | Role checks, read-only mode |
| `maintenance.php` | Role checks, read-only mode |
| `pembelian.php` | Role checks + **Approval feature** ⭐ |
| `pengeluaran.php` | Role checks, read-only mode |
| `konten.php` | Role checks, read-only mode |
| `ROLE_GUIDE.md` | NEW: Comprehensive documentation |
| `IMPLEMENTATION_SUMMARY.md` | NEW: Summary of all changes |

---

## 🔐 Security Notes

1. Passwords are hashed with MD5 in demo (use bcrypt in production)
2. All user input is escaped with `mysqli_real_escape_string()`
3. All output is escaped with `htmlspecialchars()`
4. Session-based authentication protects all pages
5. Role checks are server-side (not client-side)

---

## 🆘 Common Issues

### Q: Kepala can still edit data?
A: Check if `can_edit()` function exists in config.php and POST handlers are wrapped properly

### Q: Approve button not showing for Kepala?
A: Verify `can_approve()` function exists and pembelian.php has the approval logic block

### Q: Menu items hidden from Kepala?
A: This is intentional - all menus visible but pages are read-only. Check ROLE_GUIDE.md

### Q: Status not updating after approval?
A: Verify the URL includes `?approve=ID&status_baru=disetujui` or `ditolak`

---

## 📞 Support

For detailed information, see:
- **ROLE_GUIDE.md** - Complete role definitions and workflow
- **IMPLEMENTATION_SUMMARY.md** - Technical details of all changes
- **TESTING_CHECKLIST.md** - Comprehensive test cases

---

**Version**: 2.0  
**Last Updated**: 2024  
**Status**: ✅ Ready to Deploy
