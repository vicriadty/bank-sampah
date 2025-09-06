@extends('layouts.app')

@section('title', 'Bank Sampah - Dashboard')

@section('content')
    <div class="container">
        <h2 class="mb-4">Dashboard Bank Sampah</h2>

        <div class="row g-4">

            <!-- Jumlah Nasabah -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-primary fs-2 mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $jumlahNasabah }}</h5>
                        <small class="text-muted">Jumlah Nasabah</small>
                    </div>
                </div>
            </div>

            <!-- Jumlah Pengepul -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-success fs-2 mr-3">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $jumlahPengepul }}</h5>
                        <small class="text-muted">Jumlah Pengepul</small>
                    </div>
                </div>
            </div>

            <!-- Total Sampah Disetor -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-warning fs-2 mr-3">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ number_format($totalSampahDisetorkan, 2) }} kg</h5>
                        <small class="text-muted">Total Sampah Disetor</small>
                    </div>
                </div>
            </div>

            <!-- Total Tabungan Nasabah -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-info fs-2 mr-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalTabunganNasabah, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Tabungan Nasabah</small>
                    </div>
                </div>
            </div>

            <!-- Total Tarik Tunai -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-danger fs-2 mr-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalSaldoDitarik, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Saldo Ditarik</small>
                    </div>
                </div>
            </div>

            <!-- Total Sampah Dijual -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-secondary fs-2 mr-3">
                        <i class="fas fa-dolly"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ number_format($totalPenjualanSampah, 2) }} kg</h5>
                        <small class="text-muted">Total Sampah Dijual ke Pengepul</small>
                    </div>
                </div>
            </div>

            <!-- Total Penjualan Sampah -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-dark fs-2 mr-3">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Hasil Penjualan Sampah</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
