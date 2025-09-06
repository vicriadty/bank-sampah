@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <div class="card p-4">
        <h4>Tambah Data Sampah</h4>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('sampah.store') }}">
            @csrf

            <div class="form-group mt-3">
                <label for="jenis_sampah_id">Jenis Sampah</label>
                <select name="jenis_sampah_id" id="jenis_sampah_id" class="form-control" required>
                    <option value="">-- Pilih Jenis Sampah --</option>
                    @foreach ($jenisSampahs as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-3">
                <label for="nama_sampah">Nama Sampah</label>
                <input type="text" name="nama_sampah" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label for="harga_per_kg">Harga per Kg</label>
                <input type="number" name="harga_per_kg" step="0.01" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Simpan</button>
        </form>
    </div>
@endsection
