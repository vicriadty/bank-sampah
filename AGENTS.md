# AGENTS.md

## Project Overview

Laravel 12 bank sampah (waste bank) app with two roles: admin and nasabah.
- PHP 8.2+, Laravel 12, SB Admin 2 template, Bootstrap 5, jQuery, SweetAlert2, Select2
- PDF via barryvdh/laravel-dompdf
- Vite for asset build (though SB Admin 2 CSS is loaded from `public/template/`)

## Dev Commands

```bash
composer dev          # runs artisan serve + queue:listen + pail + vite concurrently
composer test         # clears config then runs artisan test
php artisan test      # run all tests (SQLite in-memory)
php artisan migrate:fresh --seed   # full reset + seed
```

## Architecture

- **Roles**: `admin` (full CRUD + transaksi) and `nasabah` (dashboard, riwayat, profile)
- **Layouts**: `layouts/app.blade.php` → conditionally includes `layouts/sidebar` (admin) or `layouts/sidebar-nasabah`
- **Sections**: `@yield('content')` for page body, `@yield('scripts')` for page-specific JS
- **CSS**: SB Admin 2 loaded from `public/template/css/sb-admin-2.min.css` (NOT Tailwind despite package.json)

## Key Conventions

### CRUD Modal Pattern (CREATE)
All create forms use Bootstrap 5 modals on index pages, NOT separate create.blade.php pages. Pattern:
1. Button on index page opens modal: `data-bs-toggle="modal" data-bs-target="#modalXxx"`
2. Modal body includes `_form.blade.php` partial via `@include`
3. Form submit uses AJAX with headers: `X-Requested-With: XMLHttpRequest` + `Accept: application/json`
4. Controller returns JSON: `{ success: true, redirect: url }` for AJAX, or `redirect()->route()` for non-AJAX
5. SweetAlert2 confirmation dialog before submit, success toast after
6. SweetAlert2 already loaded in layout (do NOT re-include it)

### Edit Pattern
Edit forms are currently SEPARATE pages (edit.blade.php) that extend layouts.app. They do NOT use AJAX — they use traditional form POST with `@method('PUT')`. The `edit()` method returns a view, `update()` returns redirect with flash message.

### Index Pages
Tables use `@forelse`/`@endforelse` for empty state. Delete uses hidden form + SweetAlert2 confirm. The "Aksi" column has edit (btn-warning) and delete (btn-danger) buttons.

### Flash Messages
Toast notifications use SweetAlert2 (already in layout), triggered by `session('success')` and `session('error')`.

## Database

- **Testing**: SQLite in-memory (configured in phpunit.xml)
- **Production**: MySQL (check .env)
- Key tables: users, nasabahs, dompet_nasabahs, kategori_sampahs, jenis_sampahs, setorans, setoran_details, penjualan_sampahs, detail_penjualan_sampahs, pengepuls
- Seeders in database/seeders/, factories in database/factories/

## Gotchas

- `sampah` resource routes map to JenisSampah model (restructured table). `SampahController` handles JenisSampah, not a separate Sampah model.
- Two PDF layouts per transaction: `laporan_pdf.blade.php` (full header) and `laporan_pdf2.blade.php` (simpler)
- `Nasabah::booted()` auto-creates DompetNasabah on creation — do NOT manually create dompet in controllers
- Search routes (`admin.nasabah.search`, `admin.pengepul.search`) currently return index view, not JSON
