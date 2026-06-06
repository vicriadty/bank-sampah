@extends('layouts.app')

@section('title', 'Bank Sampah - Nasabah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ubah Nasabah</h1>
    </div>

    <div class="row">
        <div class="col">
            <form action="{{ route('admin.nasabah.update', $nasabah->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="nik">NIK</label>
                            <input type="number" inputmode="numeric" name="nik" id="nik"
                                class="form-control 
                            @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $nasabah->nik) }}">
                            @error('nik')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama"
                                class="form-control 
                            @error('nama') is-invalid @enderror"
                                value="{{ old('name', $nasabah->nama) }}">
                            @error('nama')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin"
                                class="form-control 
                            @error('jenis_kelamin') is-invalid @enderror">
                                <option value="" disabled {{ old('jenis_kelamin', $nasabah->jenis_kelamin) ? '' : 'selected' }}>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin', $nasabah->jenis_kelamin) == 'Laki-laki')>Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin', $nasabah->jenis_kelamin) == 'Perempuan')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                class="form-control 
                            @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', $nasabah->tanggal_lahir) }}">
                            @error('tanggal_lahir')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir"
                                class="form-control 
                            @error('tempat_lahir') is-invalid @enderror"
                                value="{{ old('tempat_lahir', $nasabah->tempat_lahir) }}">
                            @error('tempat_lahir')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">Alamat</label>
                            <textarea name="alamat" id="alamat" cols="15" rows="5"
                                class="form-control 
                            @error('alamat') is-invalid @enderror">{{ old('alamat', $nasabah->alamat) }}</textarea>
                            @error('alamat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama">No. Handphone</label>
                            <input type="text" name="no_hp" id="no_hp"
                                class="form-control 
                            @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp', $nasabah->no_hp) }}">
                            @error('no_hp')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end" style="gap: 10px">
                                <a href="/nasabah" class="btn btn-outline-secondary">Kembali</a>
                                <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
