@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Sampah</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sampah</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sampah.update', $sampah->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="kategori_id">Kategori Sampah</label>
                            <select name="kategori_id" id="kategori_id"
                                class="form-control @error('kategori_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriSampahs as $kategori)
                                    <option value="{{ $kategori->id }}" @selected(old('kategori_id', $sampah->kategori_id) == $kategori->id)>
                                        {{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama_jenis">Nama Jenis</label>
                            <input type="text" name="nama_jenis" id="nama_jenis"
                                class="form-control 
                            @error('nama_jenis') is-invalid @enderror"
                                value="{{ old('nama_jenis', $sampah->nama_jenis) }}">
                            @error('nama_jenis')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="harga_per_kg">Harga per Kg</label>
                            <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01"
                                class="form-control @error('harga_per_kg') is-invalid @enderror"
                                value="{{ old('harga_per_kg', $sampah->harga_per_kg) }}">
                            @error('harga_per_kg')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                </div>


                <div class="form-group row mt-4 mr-2">
                    <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                        <a href="{{ route('admin.sampah.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
