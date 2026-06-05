# Implementation Plan: Optimize Auth Pages (Login & Register)

## Goal

Redesign `login.blade.php` and `register.blade.php` with a minimal clean style (white dominant + thin green accent). Replace the current SB Admin 2 template with Bootstrap 5 CDN + SweetAlert2 toasts. The register form must handle complete nasabah registration (both `users` and `nasabahs` tables).

---

## Branch

```bash
git checkout -b fix/auth-design-optimization
```

---

## Current State

| Area | Current | Target |
|------|---------|--------|
| CSS framework | SB Admin 2 (Bootstrap 4 based, local assets) | Bootstrap 5 CDN |
| Login form fields | Username + Password only | Same (keep) |
| Register form fields | Username + Email + Password + Confirm Password | **Full nasabah form** (NIK, Nama, JK, TTL, Alamat, No HP + Akun section) |
| AuthController::register | Creates `User` only | Must create `User` + `Nasabah` in transaction |
| Error display | SweetAlert2 modal on load | SweetAlert2 **toast** (top-end, auto-dismiss) |
| Layout | SB Admin 2 card | Custom centered card, white + green accent |

---

## Database Schema Reference

### `users` table
| Column | Type | Constraints |
|--------|------|-------------|
| username | string | |
| email | string | unique |
| password | string | hashed |
| role | string | default 'nasabah' |

### `nasabahs` table
| Column | Type | Constraints |
|--------|------|-------------|
| nik | string(16) | unique, 16 digits |
| nama | string(100) | |
| jenis_kelamin | enum('Laki-laki', 'Perempuan') | |
| tanggal_lahir | date | |
| tempat_lahir | string(100) | |
| alamat | text | |
| no_hp | string(15) | |
| saldo | decimal(12,2) | default 0 |

### Model `$fillable`
- **User**: `username`, `email`, `password`, `role`
- **Nasabah**: `user_id`, `nik`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `tempat_lahir`, `alamat`, `no_hp`
- **User hasOne Nasabah** / **Nasabah belongsTo User**

---

## Files to Modify

1. `resources/views/auth/login.blade.php` — Full rewrite
2. `resources/views/auth/register.blade.php` — Full rewrite
3. `app/Http/Controllers/Auth/AuthController.php` — Expand `register()` validation + nasabah creation

---

## Design System

### Color Palette
- Background: `#ffffff`
- Card: white, subtle shadow `rgba(0,0,0,0.05)`
- Primary (green accent): `#10b981` (emerald-500) — buttons, links, borders
- Primary hover: `#059669` (emerald-600)
- Text: `#1f2937` (gray-800)
- Muted text: `#6b7280` (gray-500)
- Input border: `#d1d5db` (gray-300)
- Input focus ring: `#10b981` with `rgba(16,185,129,0.15)`

### CDN Dependencies (all loaded via `<link>` / `<script>` in Blade)
- Bootstrap 5 CSS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
- Bootstrap 5 JS Bundle: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js`
- SweetAlert2: `https://cdn.jsdelivr.net/npm/sweetalert2@11`
- Flatpickr (Date Picker): `https://cdn.jsdelivr.net/npm/flatpickr`
- Flatpickr CSS: `https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css`
- Font Awesome 6 (icons): `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css`

### Toast Notifications (SweetAlert2)
- **Success toast**: `Swal.fire({ icon: 'success', title: '...', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 })`
- **Error toast**: Same pattern with `icon: 'error'` and appropriate title
- All triggered via `@if (session(...))` / `@if ($errors->any())` Blade blocks with inline `<script>`

---

## Login Page Layout

```
+----------------------------------+
|         [Logo]                   |
|         BANK SAMPAH              |
|     Selamat Datang               |
|                                  |
|  ┌──────────────────────────┐    |
|  │  Username                │    |
|  └──────────────────────────┘    |
|  ┌──────────────────────────┐    |
|  │  Password                │    |
|  └──────────────────────────┘    |
|                                  |
|  ┌──────────────────────────┐    |
|  │        Masuk             │    |
|  └──────────────────────────┘    |
|                                  |
|  Belum punya akun? Daftar        |
+----------------------------------+
  Centered card, max-width: 400px
```

### HTML Structure
```html
<body class="bg-white min-vh-100 d-flex align-items-center justify-content-center">
  <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
    <div class="card-body p-4 p-md-5">
      <!-- Brand Header: logo + title -->
      <!-- Toast scripts (success/error) -->
      <!-- Form -->
    </div>
  </div>
</body>
```

### Elements
- **BrandHeader**: Logo image (favicon1.svg, 56px) + "BANK SAMPAH" title (h4, bold, green accent) + subtitle
- **Form inputs**: Bootstrap 5 `.form-control` with `@error()` inline feedback
- **Tombol "Masuk"**: Full width, green (`btn btn-success w-100`)
- **Link daftar**: Centered below, "Belum punya akun? Daftar" with `<a href="/register">`

---

## Register Page Layout

```
+------------------------------------------+
|         [Logo]                           |
|         BANK SAMPAH                      |
|     Daftar Akun Baru                     |
|                                          |
|  ─── Data Diri ──────────────────────    |
|  ┌────────────────┐ ┌────────────────┐   |
|  │ NIK (16 digit)  │ │ Nama Lengkap  │   |
|  └────────────────┘ └────────────────┘   |
|                                          |
|  Jenis Kelamin:                          |
|  ○ Laki-laki  ○ Perempuan                |
|                                          |
|  ┌────────────────┐ ┌────────────────┐   |
|  │ Tempat Lahir   │ │ Tgl Lahir 🗓  │   |
|  └────────────────┘ └────────────────┘   |
|                                          |
|  ┌──────────────────────────────────┐    |
|  │ Alamat Lengkap                   │    |
|  │                                  │    |
|  └──────────────────────────────────┘    |
|  ┌──────────────────────────────────┐    |
|  │ No. Handphone                    │    |
|  └──────────────────────────────────┘    |
|                                          |
|  ─── Akun ───────────────────────────    |
|  ┌────────────────┐ ┌────────────────┐   |
|  │ Username       │ │ Email         │   |
|  └────────────────┘ └────────────────┘   |
|  ┌────────────────┐ ┌────────────────┐   |
|  │ Password       │ │ Konfirmasi    │   |
|  └────────────────┘ └────────────────┘   |
|                                          |
|  ┌──────────────────────────────────┐    |
|  │            Daftar                │    |
|  └──────────────────────────────────┘    |
|                                          |
|  Sudah punya akun? Masuk                 |
+------------------------------------------+
  Centered card, max-width: 640px, scrollable
```

### HTML Structure
```html
<body class="bg-white min-vh-100 d-flex align-items-center justify-content-center py-4">
  <div class="card shadow-sm" style="max-width: 640px; width: 100%;">
    <div class="card-body p-4 p-md-5">
      <!-- Brand Header -->
      <!-- Toast scripts -->
      <form>
        <!-- Section 1: Data Diri -->
        <h6 class="text-uppercase text-muted small fw-bold mb-3">Data Diri</h6>
        <hr class="mt-0">
        
        <div class="row">
          <!-- NIK (col-12 or col-md-6) -->
          <!-- Nama Lengkap (col-12 or col-md-6) -->
        </div>
        <!-- RadioGroup: Jenis Kelamin -->
        <div class="row">
          <!-- Tempat Lahir -->
          <!-- Tanggal Lahir (Flatpickr) -->
        </div>
        <!-- Alamat (textarea, full width) -->
        <!-- No. Handphone (full width) -->

        <!-- Section 2: Akun -->
        <h6 class="text-uppercase text-muted small fw-bold mb-3 mt-4">Akun</h6>
        <hr class="mt-0">
        
        <div class="row">
          <!-- Username -->
          <!-- Email -->
        </div>
        <div class="row">
          <!-- Password -->
          <!-- Konfirmasi Password -->
        </div>

        <!-- Submit -->
        <button class="btn btn-success w-100">Daftar</button>
        <!-- Link login -->
      </form>
    </div>
  </div>
</body>
```

---

## AuthController::register() — Expanded Validation & Logic

The current `register()` only validates username/email/password and creates a `User`. Must be expanded:

### Validation Rules

| Field | Rules |
|-------|-------|
| `nik` | required, string, size:16, unique:nasabahs,nik |
| `nama` | required, string, max:100 |
| `jenis_kelamin` | required, in:Laki-laki,Perempuan |
| `tempat_lahir` | required, string, max:100 |
| `tanggal_lahir` | required, date, before:today |
| `alamat` | required, string, max:1000 |
| `no_hp` | required, string, max:15 |
| `username` | required, string, max:100, unique:users,username |
| `email` | required, email, unique:users,email |
| `password` | required, min:6, confirmed |

### Store Flow (inside DB::beginTransaction)
```
1. Validate all fields
2. DB::beginTransaction()
3. Create User: User::create(['username', 'email', 'password'])
4. Create Nasabah: Nasabah::create(['user_id' => $user->id, 'nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'alamat', 'no_hp'])
5. DB::commit()
6. Redirect to login with success toast message
7. On failure: DB::rollBack(), return back with errors
```

---

## Form Field Input Types & Validation Feedback

Each field must show:
- `@error('field_name')` inline Bootstrap 5 `.invalid-feedback` or small red text
- `.is-invalid` class on the input when error exists (conditional via `$errors->has('field')`)

### Specific Field Handling

| Field | Input Type | HTML Attributes |
|-------|-----------|-----------------|
| NIK | `input type="text"` | `maxlength="16"`, `inputmode="numeric"`, `pattern="[0-9]{16}"` |
| Nama | `input type="text"` | `maxlength="100"` |
| Jenis Kelamin | radio group | Two radios with same `name="jenis_kelamin"` |
| Tempat Lahir | `input type="text"` | `maxlength="100"` |
| Tanggal Lahir | `input type="text"` | Flatpickr: `dateFormat: 'Y-m-d'`, `maxDate: 'today'` |
| Alamat | `textarea` | `rows="3"`, `maxlength="1000"` |
| No. Handphone | `input type="tel"` | `maxlength="15"` |
| Username | `input type="text"` | `maxlength="100"` |
| Email | `input type="email"` | |
| Password | `input type="password"` | `minlength="6"` |
| Konfirmasi Password | `input type="password"` | `name="password_confirmation"` |

### Flatpickr Initialization
```javascript
flatpickr("#tanggal_lahir", {
  dateFormat: "Y-m-d",
  maxDate: "today",
  allowInput: true
});
```

### RadioGroup for Jenis Kelamin
```html
<div class="d-flex gap-4">
  <div class="form-check">
    <input class="form-check-input" type="radio" name="jenis_kelamin" value="Laki-laki" id="jk_l">
    <label class="form-check-label" for="jk_l">Laki-laki</label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="jenis_kelamin" value="Perempuan" id="jk_p">
    <label class="form-check-label" for="jk_p">Perempuan</label>
  </div>
</div>
```

---

## Implementation Steps

### Step 1: Create branch
```bash
git checkout -b fix/auth-design-optimization
```

### Step 2: Update AuthController.php
- Expand `register()` validation with all nasabah fields
- Add DB transaction to create User + Nasabah
- Add error messages in Indonesian (e.g., "NIK harus 16 digit.", "Email sudah terdaftar.")
- Add username unique validation (currently missing)
- Keep login logic unchanged

### Step 3: Rewrite login.blade.php
- Full HTML with Bootstrap 5 CDN (remove all SB Admin 2 assets)
- Centered card, max-width 400px
- Brand logo + title
- Username + Password fields with validation feedback
- Masuk button (green, full width)
- "Belum punya akun? Daftar" link
- SweetAlert2 toast for session('error') and validation errors

### Step 4: Rewrite register.blade.php
- Full HTML with Bootstrap 5 CDN
- Centered card, max-width 640px, scrollable (min-vh-100 with py-4)
- BrandHeader
- **Section 1 — Data Diri**: NIK, Nama, JK (radio), Tempat Lahir, Tanggal Lahir (Flatpickr), Alamat, No HP
- **Section 2 — Akun**: Username, Email, Password, Confirm Password
- Separator with `<hr>` + section heading
- Daftar button (full width, green)
- "Sudah punya akun? Masuk" link
- Flatpickr CDN + initialization
- SweetAlert2 toast for success/error
- Old input preservation with `old()` on all fields
- Radio button checked state via `old('jenis_kelamin')`

### Step 5: Verify and commit
- PHP syntax check: `php -l` on controller
- Review both Blade files for consistency
- Commit with appropriate message

---

## Success Criteria

- [ ] Login page: card centered, clean white + green accent, shows validation toast on error
- [ ] Register page: two sections with separator, all fields present and aligned to migrations
- [ ] Register: NIK validated as 16-digit numeric unique
- [ ] Register: Tanggal Lahir uses Flatpickr date picker
- [ ] Register: Radio group for Jenis Kelamin (Laki-laki / Perempuan)
- [ ] Register: Submitting creates both `User` (role='nasabah') and `Nasabah` records
- [ ] Register: On success → redirect to login with toast "Register berhasil! Silakan login!"
- [ ] Both pages: errors shown as SweetAlert2 toasts + inline field errors
- [ ] Both pages: Bootstrap 5 CDN only (no local SB Admin 2 assets)
- [ ] Both pages: old input preserved on validation failure

---

## Suggested Validation Messages (Indonesian)

| Field | Error Message |
|-------|--------------|
| nik.required | NIK wajib diisi. |
| nik.size | NIK harus 16 digit. |
| nik.unique | NIK sudah terdaftar. |
| nama.required | Nama lengkap wajib diisi. |
| jenis_kelamin.required | Pilih jenis kelamin. |
| jenis_kelamin.in | Jenis kelamin tidak valid. |
| tempat_lahir.required | Tempat lahir wajib diisi. |
| tanggal_lahir.required | Tanggal lahir wajib diisi. |
| tanggal_lahir.before | Tanggal lahir harus sebelum hari ini. |
| alamat.required | Alamat wajib diisi. |
| no_hp.required | No. handphone wajib diisi. |
| username.required | Username wajib diisi. |
| username.unique | Username sudah digunakan. |
| email.required | Email wajib diisi. |
| email.email | Format email tidak valid. |
| email.unique | Email sudah terdaftar. |
| password.required | Password wajib diisi. |
| password.min | Password minimal 6 karakter. |
| password.confirmed | Konfirmasi password tidak cocok. |

---

## Notes for AI Agent

- Do NOT modify any routes or add new controllers — only update AuthController methods
- Keep login controller logic exactly as-is (no changes needed)
- The old input should be preserved via `old('field_name')` on every form field
- Radio buttons need manual `checked` attribute: `{{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }}`
- Flatpickr only needed on register page
- SweetAlert2 toast configuration: `{ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true }`
- Both views are standalone (not extending any layout) — complete `<html>` documents
- Keep the favicon: `<link rel="icon" href="{{ asset('favicon1.svg') }}">`
