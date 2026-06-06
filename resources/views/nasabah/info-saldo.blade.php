@extends('layouts.app')

@section('title', 'Info Saldo')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Informasi Saldo</h1>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Saldo Saat Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Arus Kas</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-success">Riwayat Setoran (Masuk)</h5>
                <ul class="list-group">
                    @forelse($setorans as $setoran)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $setoran->created_at->format('d/m/Y') }}
                        <span class="text-success font-weight-bold">+Rp {{ number_format($setoran->total_harga, 0, ',', '.') }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-muted">Belum ada setoran.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
