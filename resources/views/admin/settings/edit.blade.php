@extends('layouts.app')

@section('title', 'Bank Sampah - Profile')

@section('content')
    @if (session('success'))
        <script>
            Swal.fire({
                position: "top-end",
                text: "{{ session('success') }}",
                icon: "success",
                width: 600,
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="container d-flex justify-content-center mt-5">
        <div class="col-md-6 mr-5">
            <h3>Pengaturan Akun</h3>

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="{{ old('name', $user->username) }}">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $user->email) }}">
                </div>
                <div class="mb-3">
                    <label>Foto Profil</label><br>
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" width="100" class="mb-2"><br>
                    @endif
                    <input type="file" name="photo" class="form-control">
                </div>
                <button class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>

        <div class="col-md-6">
            <h4>Ganti Password</h4>
            <form action="{{ route('settings.updatePassword') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Password Lama</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-control">
                </div>
                <button class="btn btn-warning">Ubah Password</button>
            </form>
        </div>
    </div>
    </div>
@endsection
