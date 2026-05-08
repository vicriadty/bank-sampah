@extends('layouts.app')

@section('title', 'Bank Sampah - Tarik Saldo')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penarikan Saldo</h6>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('admin.tarik-saldo.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Nasabah</label>
                            <select name="nasabah_id" class="form-control" required>
                                <option value="">-- Pilih Nasabah --</option>
                                @foreach ($nasabahs as $nasabah)
                                    <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} (Saldo:
                                        Rp{{ number_format($nasabah->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>Jumlah Tarik</label>
                            <input type="number" name="jumlah_tarik" class="form-control" required min="1">
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.tarik-saldo.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
