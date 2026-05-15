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

    <!-- Total Emas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Emas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalGoldGrams, 4, ',', '.') }} g</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gold Price + Estimasi Card -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow border-left-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-warning mb-1"><i class="fas fa-coins"></i> Harga Emas Terkini</h6>
                        @if($goldPrice['price_per_gram'] > 0)
                            <h4 class="font-weight-bold mb-0">Rp {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}/gram</h4>
                        @else
                            <h4 class="font-weight-bold mb-0 text-muted">Tidak tersedia</h4>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Estimasi Nilai Emas Anda</small>
                        @if($goldPrice['price_per_gram'] > 0 && $totalGoldGrams > 0)
                            <h5 class="font-weight-bold text-success">Rp {{ number_format($totalGoldGrams * $goldPrice['price_per_gram'], 0, ',', '.') }}</h5>
                        @else
                            <h5 class="font-weight-bold text-muted">-</h5>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('nasabah.gold-exchange.create') }}" class="btn btn-warning">
                            <i class="fas fa-exchange-alt"></i> Tukar Saldo ke Emas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">

    <!-- Line Chart: Setoran per Bulan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Setoran per Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="setoranChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Bar Chart: Penarikan per Bulan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Penarikan per Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="penarikanChart" height="250"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Welcome Card -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, {{ $nasabah->nama }}!</h6>
            </div>
            <div class="card-body">
                <p>Melalui dashboard ini, Anda dapat memantau saldo tabungan sampah Anda, melihat riwayat transaksi, melakukan permintaan pencairan saldo menjadi uang tunai, atau menukarkan saldo menjadi emas.</p>
                <div class="mt-4">
                    <a href="{{ route('nasabah.pencairan.create') }}" class="btn btn-success btn-icon-split mr-2">
                        <span class="icon text-white-50">
                            <i class="fas fa-hand-holding-usd"></i>
                        </span>
                        <span class="text">Request Pencairan Saldo</span>
                    </a>
                    <a href="{{ route('nasabah.gold-exchange.create') }}" class="btn btn-warning btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-coins"></i>
                        </span>
                        <span class="text">Tukar Saldo ke Emas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Setoran per Bulan (Line Chart)
    var setoranData = @json($setoranPerBulan);

    new Chart(document.getElementById('setoranChart'), {
        type: 'line',
        data: {
            labels: setoranData.map(i => i.bulan),
            datasets: [{
                label: 'Setoran (Rp)',
                data: setoranData.map(i => parseFloat(i.total)),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Penarikan per Bulan (Bar Chart)
    var penarikanData = @json($penarikanPerBulan);

    new Chart(document.getElementById('penarikanChart'), {
        type: 'bar',
        data: {
            labels: penarikanData.map(i => i.bulan),
            datasets: [{
                label: 'Penarikan (Rp)',
                data: penarikanData.map(i => parseFloat(i.total)),
                backgroundColor: 'rgba(54, 185, 204, 0.7)',
                borderColor: 'rgba(54, 185, 204, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
