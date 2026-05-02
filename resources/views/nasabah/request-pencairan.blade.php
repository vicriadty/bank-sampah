@extends('layouts.app')

@section('title', 'Request Pencairan')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Request Pencairan Saldo</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pengajuan Pencairan</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Saldo Aktif: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</strong>
                    <br>
                    <small>* Minimal penarikan Rp 10.000</small>
                </div>

                <form action="{{ route('nasabah.pencairan.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="jumlah_tarik">Jumlah yang Ingin Dicairkan (Rp)</label>
                        <input type="number" name="jumlah_tarik" id="jumlah_tarik" class="form-control @error('jumlah_tarik') is-invalid @enderror" value="{{ old('jumlah_tarik') }}" required min="10000" max="{{ $nasabah->saldo }}">
                        @error('jumlah_tarik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-block">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
