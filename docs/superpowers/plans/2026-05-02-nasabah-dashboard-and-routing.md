# Pengembangan Fitur Dashboard Nasabah & Refactoring Routing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Memisahkan ruang kerja antara Admin dan Nasabah, refactoring sistem routing dengan prefix /admin dan /nasabah, serta pembuatan dashboard khusus Nasabah dengan fitur pengajuan pencairan saldo.

**Architecture:** Menggunakan Laravel Route Grouping dengan prefix dan middleware 'role' untuk memisahkan akses. Implementasi workflow approval untuk penarikan saldo nasabah dengan menambahkan kolom status pada tabel tarik_saldos.

**Tech Stack:** Laravel, PHP, Blade, SB Admin 2 (Bootstrap 4).

---

### Task 1: Database Migration for Withdrawal Status

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_status_to_tarik_saldos_table.php`
- Modify: `app/Models/TarikSaldo.php`

- [ ] **Step 1: Create migration file**
Run: `php artisan make:migration add_status_to_tarik_saldos_table --table=tarik_saldos`

- [ ] **Step 2: Add status column in migration**
```php
public function up(): void
{
    Schema::table('tarik_saldos', function (Blueprint $table) {
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('jumlah_tarik');
        $table->text('keterangan')->nullable()->after('status');
    });
}
```

- [ ] **Step 3: Run migration**
Run: `php artisan migrate`

- [ ] **Step 4: Update TarikSaldo Model**
Add `status` and `keterangan` to `$fillable`.

- [ ] **Step 5: Commit**
```bash
git add .
git commit -m "feat: add status column to tarik_saldos table"
```

### Task 2: Refactoring Routing

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Group Admin routes**
Move existing admin routes into a group with prefix `admin` and name `admin.`.

- [ ] **Step 2: Create Nasabah route group**
Add a new group for nasabah with prefix `nasabah` and name `nasabah.`.

- [ ] **Step 3: Update AuthController redirect**
Ensure the `login` method in `AuthController` redirects to the correct prefixed routes.

- [ ] **Step 4: Commit**
```bash
git add routes/web.php app/Http/Controllers/Auth/AuthController.php
git commit -m "refactor: group routes by role and prefix"
```

### Task 3: Sidebar & Layout Logic

**Files:**
- Create: `resources/views/layouts/sidebar-nasabah.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Create Nasabah Sidebar**
Duplicate `sidebar.blade.php` to `sidebar-nasabah.blade.php` and update the menu items for Nasabah.

- [ ] **Step 2: Update App Layout**
Add conditional logic to include either the Admin sidebar or the Nasabah sidebar.

- [ ] **Step 3: Commit**
```bash
git add resources/views/layouts/
git commit -m "feat: implement role-based sidebar"
```

### Task 4: Nasabah Controllers

**Files:**
- Create: `app/Http/Controllers/Nasabah/DashboardController.php`
- Create: `app/Http/Controllers/Nasabah/TransaksiController.php`
- Create: `app/Http/Controllers/Nasabah/PencairanController.php`
- Create: `app/Http/Controllers/Nasabah/ProfileController.php`

- [ ] **Step 1: Implement Nasabah DashboardController**
Implement `index` and `infoSaldo` methods.

- [ ] **Step 2: Implement Nasabah TransaksiController**
Implement `index` to show combined history.

- [ ] **Step 3: Implement Nasabah PencairanController**
Implement `create` and `store` (initial request with pending status).

- [ ] **Step 4: Commit**
```bash
git add app/Http/Controllers/Nasabah/
git commit -m "feat: implement nasabah controllers"
```

### Task 5: Nasabah Views

**Files:**
- Create: `resources/views/nasabah/dashboard.blade.php`
- Create: `resources/views/nasabah/riwayat-transaksi.blade.php`
- Create: `resources/views/nasabah/info-saldo.blade.php`
- Create: `resources/views/nasabah/request-pencairan.blade.php`
- Create: `resources/views/nasabah/profile.blade.php`

- [ ] **Step 1: Implement Dashboard View**
Show summary cards using SB Admin 2 classes.

- [ ] **Step 2: Implement Request Pencairan View**
Create the form with balance validation.

- [ ] **Step 3: Commit**
```bash
git add resources/views/nasabah/
git commit -m "feat: implement nasabah views"
```

### Task 6: Admin Approval Feature

**Files:**
- Modify: `app/Http/Controllers/Admin/TarikSaldoController.php`
- Modify: `resources/views/pages/transaksi/tarik-saldo/index.blade.php`

- [ ] **Step 1: Update Admin TarikSaldoController**
Add methods to `approve` and `reject` requests. Approve method must decrement the balance.

- [ ] **Step 2: Update Admin TarikSaldo Index View**
Show the status and add buttons for Approve/Reject.

- [ ] **Step 3: Commit**
```bash
git add .
git commit -m "feat: implement admin approval for withdrawal requests"
```

### Task 7: Final Verification & Cleanup

- [ ] **Step 1: Run tests (if any)**
- [ ] **Step 2: Verify all links**
Ensure `route()` calls in all modified views are updated to include the `admin.` or `nasabah.` prefix.
- [ ] **Step 3: Commit**
```bash
git add .
git commit -m "fix: final cleanup and link verification"
```
