@extends('layouts.app')

@section('title', 'Bank Sampah - Pengepul')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pengepul</h1>
    </div>

    <div class="row">
        <div class="col">
            <form action="/pengepul" method="post">
                @csrf
                @method('POST')
                <div class="card">
                    <div class="card-body">
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
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <option value="" disabled selected>-- Pilih Status --</option>
                                @foreach ([
            (object)
    [
                'label' => 'Aktif',
                'value' => 'Aktif',
            ],
            (object) [
                'label' => 'Tidak Aktif',
                'value' => 'Tidak Aktif',
            ],
        ] as $item)
                                    <option value="{{ $item->value }}" @selected(old('status'))>{{ $item->label }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end" style="gap: 10px">
                                <a href="/pengepul" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
