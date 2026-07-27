<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bank Sampah - Daftar Akun">
    <title>Bank Sampah - Daftar</title>

    <link rel="icon" href="{{ asset('favicon1.svg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            padding: 1.5rem 0;
        }

        .auth-card {
            max-width: 640px;
            width: 100%;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .auth-card .card-body {
            padding: 2.5rem 2rem;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            margin-bottom: 0.5rem;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #10b981;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 1.75rem;
        }

        .section-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .section-divider {
            margin-top: 0;
            margin-bottom: 1.25rem;
            border-color: #e5e7eb;
            opacity: 0.8;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #d1d5db;
            padding: 0.55rem 0.9rem;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .btn-success {
            background-color: #10b981;
            border-color: #10b981;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.2s;
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }

        .auth-link {
            color: #6b7280;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .auth-link:hover {
            color: #10b981;
        }

        .invalid-feedback {
            font-size: 0.78rem;
            margin-top: 0.2rem;
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .input-icon-left {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .form-control {
            padding-left: 2.4rem;
        }
    </style>
</head>

<body>

    <div class="auth-card card shadow-sm mx-3">
        <div class="card-body">

            {{-- Toast: Success --}}
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '{{ session('success') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Toast: Error from session --}}
            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: '{{ session('error') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Toast: Validation errors --}}
            @if ($errors->any())
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Brand Header --}}
            <div class="text-center">
                <img src="{{ asset('favicon1.svg') }}" alt="Bank Sampah" class="brand-logo">
                <h1 class="brand-title">BANK SAMPAH</h1>
                <p class="brand-subtitle">Daftar Akun Baru</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf
                @method('POST')

                {{-- Section 1: Data Diri --}}
                <div class="section-heading">Data Diri</div>
                <hr class="section-divider">

                <div class="row g-3">
                    {{-- NIK --}}
                    <div class="col-md-6">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" name="nik" id="nik"
                               class="form-control @error('nik') is-invalid @enderror"
                               placeholder="16 digit NIK" value="{{ old('nik') }}"
                               maxlength="16" inputmode="numeric" pattern="[0-9]{16}" required>
                        @error('nik')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Nama lengkap" value="{{ old('nama') }}" maxlength="100" required>
                        @error('nama')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Jenis Kelamin --}}
                <div class="mt-3">
                    <label class="form-label d-block">Jenis Kelamin</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror"
                                   type="radio" name="jenis_kelamin" value="Laki-laki" id="jk_l"
                                   {{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="jk_l">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror"
                                   type="radio" name="jenis_kelamin" value="Perempuan" id="jk_p"
                                   {{ old('jenis_kelamin') === 'Perempuan' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="jk_p">Perempuan</label>
                        </div>
                    </div>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mt-2">
                    {{-- Tempat Lahir --}}
                    <div class="col-md-6">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                               class="form-control @error('tempat_lahir') is-invalid @enderror"
                               placeholder="Kota lahir" value="{{ old('tempat_lahir') }}" maxlength="100" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Lahir (Flatpickr) --}}
                    <div class="col-md-6">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="text" name="tanggal_lahir" id="tanggal_lahir"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               placeholder="Pilih tanggal" value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mt-3">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" id="alamat"
                              class="form-control @error('alamat') is-invalid @enderror"
                              placeholder="Alamat lengkap" rows="3" maxlength="1000" required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- No. Handphone --}}
                <div class="mt-3">
                    <label for="no_hp" class="form-label">No. Handphone</label>
                    <input type="tel" name="no_hp" id="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}" maxlength="15" required>
                    @error('no_hp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Section 2: Akun --}}
                <div class="section-heading mt-4">Akun</div>
                <hr class="section-divider">

                <div class="row g-3">
                    {{-- Username --}}
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon-left"></i>
                            <input type="text" name="username" id="username"
                                   class="form-control @error('username') is-invalid @enderror"
                                   placeholder="Username" value="{{ old('username') }}"
                                   minlength="5" maxlength="30" required>
                        </div>
                        @error('username')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    {{-- Password --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 karakter" minlength="8" maxlength="100" required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi password" minlength="8" maxlength="100" required>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-success w-100 mt-4">
                    <i class="fas fa-user-plus me-2"></i>Daftar
                </button>
            </form>

            {{-- Login Link --}}
            <div class="text-center mt-4">
                <span class="auth-link">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="auth-link fw-semibold" style="color: #10b981;">
                    Masuk
                </a>
            </div>

        </div>
    </div>

    {{-- Flatpickr Init --}}
    <script>
        flatpickr("#tanggal_lahir", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            allowInput: true
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
