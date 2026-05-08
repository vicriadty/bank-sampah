# Design Doc: Pengembangan Fitur Dashboard Nasabah & Refactoring Routing

## 1. Overview
Implementasi pemisahan ruang kerja antara Admin dan Nasabah, refactoring sistem routing dengan prefix, serta pembuatan dashboard khusus Nasabah.

## 2. Architecture & Design

### 2.1 Routing Structure
Refactoring `routes/web.php` untuk menggunakan grouping, prefix, dan naming convention yang konsisten.

- **Admin Routes**:
  - Prefix: `/admin`
  - Name Prefix: `admin.`
  - Middleware: `['auth', 'role:admin']`
  - Controllers: `App\Http\Controllers\Admin\*`
- **Nasabah Routes**:
  - Prefix: `/nasabah`
  - Name Prefix: `nasabah.`
  - Middleware: `['auth', 'role:nasabah']`
  - Controllers: `App\Http\Controllers\Nasabah\*`

### 2.2 User Interface (SB Admin 2)
Menggunakan template SB Admin 2 yang sudah ada untuk konsistensi.

- **Layouts**:
  - Modifikasi `resources/views/layouts/app.blade.php` untuk conditional sidebar.
  - Sidebar baru: `resources/views/layouts/sidebar-nasabah.blade.php`.
- **Views**:
  - `resources/views/nasabah/dashboard.blade.php`: Summary cards & quick actions.
  - `resources/views/nasabah/riwayat-transaksi.blade.php`: Table of deposits/withdrawals.
  - `resources/views/nasabah/info-saldo.blade.php`: Detailed cashflow.
  - `resources/views/nasabah/request-pencairan.blade.php`: Withdrawal request form.
  - `resources/views/nasabah/profile.blade.php`: Profile details.

### 2.3 Data Flow & Logic

#### Withdrawal Request (Pencairan)
1.  **Migration**: Tambahkan kolom `status` (`pending`, `approved`, `rejected`) ke tabel `tarik_saldos`. Default: `pending`.
2.  **Nasabah Flow**:
    - Mengisi form pencairan.
    - Validasi: `jumlah_tarik <= saldo_aktif`.
    - Simpan ke `tarik_saldos` dengan `status = 'pending'`.
3.  **Admin Flow**:
    - Melihat daftar pencairan pending.
    - Tombol Approve/Reject.
    - **Approve**: Set `status = 'approved'`, kurangi `saldo` di tabel `nasabahs`.
    - **Reject**: Set `status = 'rejected'`.

## 3. Technical Specs

### 3.1 Controllers to Create
- `Nasabah\DashboardController`
- `Nasabah\TransaksiController`
- `Nasabah\PencairanController`
- `Nasabah\ProfileController`

### 3.2 Database Changes
- Migration: `add_status_to_tarik_saldos_table`

## 4. Implementation Steps
1.  Buat Migration untuk status `tarik_saldos`.
2.  Refactor `routes/web.php` (Admin grouping).
3.  Implementasi `sidebar-nasabah.blade.php` dan update `app.blade.php`.
4.  Buat Controller Nasabah.
5.  Buat View Nasabah.
6.  Update `AuthController` untuk role-based redirect.
7.  Update `Admin\TarikSaldoController` untuk fitur approval.

## 5. Success Criteria
- Route `/admin/...` hanya bisa diakses admin.
- Route `/nasabah/...` hanya bisa diakses nasabah.
- Saldo nasabah hanya berkurang SETELAH admin menyetujui request pencairan.
- Semua link navigasi berfungsi dengan prefix baru.
