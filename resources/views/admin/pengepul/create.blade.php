@extends('layouts.app')

@section('title', 'Bank Sampah - Pengepul')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pengepul</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengepul Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pengepul.store') }}" method="post">
                        @csrf
                        @method('POST')
                        <div class="form-group mb-3">
                            <label for="nama">Nama Pengepul</label>
                            <input type="text" name="nama" id="nama"
                                class="form-control 
                            @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}">
                            @error('nama')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" cols="15" rows="5"
                                class="form-control 
                            @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="no_hp">No. Handphone</label>
                            <input type="number" name="no_hp" id="no_hp"
                                class="form-control 
                            @error('no_hp') is-invalid @enderror"
                                value="{{ old('no_hp') }}">
                            @error('no_hp')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status"
                                class="form-control 
                            @error('status') is-invalid @enderror">
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="Aktif" @selected(old('status') == 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected(old('status') == 'Tidak Aktif')>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" cols="15" rows="5"
                                class="form-control 
                            @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.pengepul.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
