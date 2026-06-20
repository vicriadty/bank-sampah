@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Nasabah</h1>
    </div>

    <!-- Content Row - 4 Cards -->
    <div class="row">

        <!-- Saldo Aktif -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Saldo Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($saldoAktif, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Emas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Saldo Emas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ rtrim(rtrim(number_format($saldoEmas, 4, ',', '.'), '0'), ',') }} g
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Di Konversi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Saldo Di Konversi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($saldoDiKonversi, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Setoran -->
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

    </div>

    <!-- Gold Price + Estimasi -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h6 class="font-weight-bold text-warning mb-1"><i class="fas fa-coins"></i> Harga Emas Terkini
                            </h6>
                            @if ($goldPrice['price_per_gram'] > 0)
                                <h4 class="font-weight-bold mb-0">Rp
                                    {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}/gram</h4>
                            @else
                                <h4 class="font-weight-bold mb-0 text-muted">Tidak tersedia</h4>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Estimasi Nilai Emas Anda</small>
                            @if ($goldPrice['price_per_gram'] > 0 && $saldoEmas > 0)
                                <h5 class="font-weight-bold text-success">Rp
                                    {{ number_format($saldoEmas * $goldPrice['price_per_gram'], 0, ',', '.') }}</h5>
                            @else
                                <h5 class="font-weight-bold text-muted">-</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">

        <!-- Bar Chart: Setoran per Bulan -->
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

        <!-- Bar Chart: Saldo Di Konversi per Bulan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Saldo Di Konversi (Rupiah ke Emas)</h6>
                </div>
                <div class="card-body">
                    <canvas id="konversiChart" height="250"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Setoran per Bulan (Bar Chart)
        var setoranData = @json($setoranPerBulan);

        new Chart(document.getElementById('setoranChart'), {
            type: 'bar',
            data: {
                labels: setoranData.map(i => i.bulan),
                datasets: [{
                    label: 'Setoran (Rp)',
                    data: setoranData.map(i => parseFloat(i.total)),
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
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

        // Saldo Di Konversi per Bulan (Bar Chart)
        var konversiData = @json($konversiPerBulan);

        new Chart(document.getElementById('konversiChart'), {
            type: 'bar',
            data: {
                labels: konversiData.map(i => i.bulan),
                datasets: [{
                    label: 'Rupiah Dikonversi',
                    data: konversiData.map(i => parseFloat(i.total_rupiah)),
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1,
                    order: 1
                }, {
                    label: 'Emas (gram)',
                    data: konversiData.map(i => parseFloat(i.total_gram)),
                    backgroundColor: 'rgba(212, 160, 23, 0.7)',
                    borderColor: 'rgba(212, 160, 23, 1)',
                    borderWidth: 1,
                    order: 2
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
