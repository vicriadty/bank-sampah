@extends('layouts.app')

@section('title', 'Bank Sampah - Tarik Saldo')

@section('content')
    <div class="container">
        <h4>Tambah Tarik Saldo</h4>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.tarik-saldo.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nasabah</label>
                <select name="nasabah_id" class="form-control" required>
                    <option value="">-- Pilih Nasabah --</option>
                    @foreach ($nasabahs as $nasabah)
                        <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} (Saldo:
                            Rp{{ number_format($nasabah->saldo, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-3">
                <label>Jumlah Tarik</label>
                <input type="number" name="jumlah_tarik" class="form-control" required min="1">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
@endsection
