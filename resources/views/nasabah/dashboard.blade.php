@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Nasabah</h1>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Saldo Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Saldo Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Setoran Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Setoran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSetoran }} Kali</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-upload fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Penarikan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Penarikan Berhasil</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTarik }} Kali</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, {{ $nasabah->nama }}!</h6>
            </div>
            <div class="card-body">
                <p>Melalui dashboard ini, Anda dapat memantau saldo tabungan sampah Anda, melihat riwayat transaksi, dan melakukan permintaan pencairan saldo menjadi uang tunai.</p>
                <div class="mt-4">
                    <a href="{{ route('nasabah.pencairan.create') }}" class="btn btn-success btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-hand-holding-usd"></i>
                        </span>
                        <span class="text">Request Pencairan Saldo</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
