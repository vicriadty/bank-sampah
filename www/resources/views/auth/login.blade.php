<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bank Sampah - Login">
    <title>Bank Sampah - Login</title>

    <link rel="icon" href="{{ asset('favicon1.svg') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        .auth-card {
            max-width: 400px;
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

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #d1d5db;
            padding: 0.625rem 1rem;
            font-size: 0.95rem;
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

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .form-control {
            padding-left: 2.6rem;
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
            font-size: 0.8rem;
            margin-top: 0.25rem;
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
                        timer: 2000,
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
                        timer: 2000,
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
                        timer: 2000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Brand Header --}}
            <div class="text-center">
                <img src="{{ asset('favicon1.svg') }}" alt="Bank Sampah" class="brand-logo">
                <h1 class="brand-title">BANK SAMPAH</h1>
                <p class="brand-subtitle">Selamat Datang</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf
                @method('POST')

                <div class="mb-3">
                    <label for="username" class="form-label fw-medium text-secondary small">Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username"
                            class="form-control @error('username') is-invalid @enderror" placeholder="Masukkan username"
                            value="{{ old('username') }}" required autofocus>
                    </div>
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium text-secondary small">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password"
                            required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>

            {{-- Register Link --}}
            <div class="text-center mt-4">
                <span class="auth-link">Belum punya akun?</span>
                <a href="{{ route('register') }}" class="auth-link fw-semibold" style="color: #10b981;">
                    Daftar
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
