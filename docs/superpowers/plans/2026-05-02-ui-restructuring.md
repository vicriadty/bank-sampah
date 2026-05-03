# Bank Sampah UI Restructuring Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Clean up the view directory structure, fix the broken setoran AJAX, and standardize admin form UIs.

**Architecture:** Move views from `pages/` to role-based directories (`admin/`, `auth/`, root) and update controller references. Apply a unified Bootstrap Card design to all "Create" forms.

**Tech Stack:** Laravel (Blade), Bootstrap 4 (SB Admin 2), jQuery (for AJAX).

---

### Task 1: View File Migration & Cleanup

**Files:**
- Move: `resources/views/pages/*` to various locations
- Delete: `resources/views/pages`

- [ ] **Step 1: Create new directories**
Run: `mkdir -p resources/views/admin/transaksi resources/views/auth`

- [ ] **Step 2: Move Admin views**
Run:
```bash
mv resources/views/pages/nasabah resources/views/admin/
mv resources/views/pages/pengepul resources/views/admin/
mv resources/views/pages/sampah resources/views/admin/
mv resources/views/pages/stok-sampah resources/views/admin/
mv resources/views/pages/settings resources/views/admin/
mv resources/views/pages/transaksi/setor-sampah resources/views/admin/transaksi/
mv resources/views/pages/transaksi/tarik-saldo resources/views/admin/transaksi/
mv resources/views/pages/transaksi/penjualan-sampah resources/views/admin/transaksi/
mv resources/views/pages/admin/dashboard.blade.php resources/views/admin/dashboard.blade.php
```

- [ ] **Step 3: Move Auth and Public views**
Run:
```bash
mv resources/views/pages/auth/* resources/views/auth/
mv resources/views/pages/landing.blade.php resources/views/landing.blade.php
```

- [ ] **Step 4: Cleanup empty directories**
Run: `rm -rf resources/views/pages`

- [ ] **Step 5: Commit**
```bash
git add resources/views/
git commit -m "refactor: restructure view directories to role-based folders"
```

---

### Task 2: Controller Reference Updates

**Files:**
- Modify: `app/Http/Controllers/Admin/*.php`
- Modify: `app/Http/Controllers/Auth/*.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Update Admin Controllers**
Search all files in `app/Http/Controllers/Admin/` for `view('pages.` and replace with `view('admin.`.
Specifically check:
- `DashboardController` (`pages.admin.dashboard` -> `admin.dashboard`)
- `NasabahController` (`pages.nasabah.index` -> `admin.nasabah.index`)
- `PengepulController` (`pages.pengepul.index` -> `admin.pengepul.index`)
- `SampahController` (`pages.sampah.index` -> `admin.sampah.index`)
- `SetoranController` (`pages.transaksi.setor-sampah.index` -> `admin.transaksi.setor-sampah.index`)
- `TarikSaldoController` (`pages.transaksi.tarik-saldo.index` -> `admin.transaksi.tarik-saldo.index`)
- `PenjualanSampahController` (`pages.transaksi.penjualan-sampah.index` -> `admin.transaksi.penjualan-sampah.index`)

- [ ] **Step 2: Update Auth Controller**
Update `AuthController` (or login controller) to use `view('auth.login')` instead of `view('pages.auth.login')`.

- [ ] **Step 3: Update Routes**
Check `routes/web.php` for any direct `view()` returns (e.g. landing page).
Update `view('pages.landing')` to `view('landing')`.

- [ ] **Step 4: Commit**
```bash
git add app/Http/Controllers/ routes/web.php
git commit -m "refactor: update controller and route view references"
```

---

### Task 3: AJAX Bug Fix (Setoran Form)

**Files:**
- Modify: `resources/views/admin/transaksi/setor-sampah/create.blade.php`
- Modify: `resources/views/landing.blade.php`

- [ ] **Step 1: Update AJAX URL in Admin Form**
In `resources/views/admin/transaksi/setor-sampah/create.blade.php`, find:
`url: '/get-sampah-by-jenis/' + jenisID,`
Replace with:
`url: '/admin/get-sampah-by-jenis/' + jenisID,`

- [ ] **Step 2: Update AJAX URL in Landing Page**
In `resources/views/landing.blade.php`, verify the AJAX URL for the setoran form uses the `/admin/` prefix.

- [ ] **Step 3: Commit**
```bash
git commit -m "fix: update AJAX endpoint for waste category selection"
```

---

### Task 4: UI Standardization - Core Management

**Files:**
- Modify: `resources/views/admin/nasabah/create.blade.php`
- Modify: `resources/views/admin/pengepul/create.blade.php`

- [ ] **Step 1: Standardize Nasabah Create UI**
Wrap the form in:
```html
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Nasabah Baru</h6>
            </div>
            <div class="card-body">
                <!-- Existing Form Here -->
            </div>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Standardize Pengepul Create UI**
Apply the same card structure to `resources/views/admin/pengepul/create.blade.php`.

- [ ] **Step 3: Commit**
```bash
git commit -m "ui: standardize nasabah and pengepul create forms"
```

---

### Task 5: UI Standardization - Transaction Forms

**Files:**
- Modify: `resources/views/admin/transaksi/tarik-saldo/create.blade.php`
- Modify: `resources/views/admin/transaksi/setor-sampah/create.blade.php`
- Modify: `resources/views/admin/transaksi/penjualan-sampah/create.blade.php`

- [ ] **Step 1: Standardize Withdrawal Form**
Apply the card structure to `tarik-saldo/create.blade.php`.

- [ ] **Step 2: Standardize Deposit Form**
Apply the card structure to `setor-sampah/create.blade.php`.

- [ ] **Step 3: Standardize Sale Form**
Apply the card structure to `penjualan-sampah/create.blade.php`. (Use `col-lg-12` if the form contains wide tables).

- [ ] **Step 4: Commit**
```bash
git commit -m "ui: standardize transaction create forms"
```
