@extends('layouts.app')

@section('title', 'Bank Sampah - Dashboard')

@section('content')
    <div class="container-fluid">

        <!-- Hero: Harga Emas Terkini -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning text-white shadow-lg border-0">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <i class="fas fa-coins" style="font-size: 4rem;"></i>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-light mb-1">Harga Emas 24K / Gram</h5>
                                @if ($goldPrice['price_per_gram'] > 0)
                                    <h2 class="font-weight-bold mb-0" style="font-size: 2.5rem;">
                                        Rp {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}
                                    </h2>
                                @else
                                    <h2 class="font-weight-bold mb-0">Tidak tersedia</h2>
                                @endif
                            </div>
                            <div class="col-md-4 text-md-right">
                                @if (!empty($goldPrice['updated_at']))
                                    <small class="d-block"><i class="fas fa-sync-alt"></i> Diperbarui: {{ $goldPrice['updated_at'] }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 1: Master Data -->
        <h5 class="text-muted mb-3"><i class="fas fa-database"></i> Master Data</h5>
        <div class="row g-4 mb-4">

            <div class="col-md-4 mb-3">
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

            <div class="col-md-4 mb-3">
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

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-info fs-2 mr-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $jumlahSampah }}</h5>
                        <small class="text-muted">Jenis Sampah</small>
                    </div>
                </div>
            </div>

        </div>

        <!-- Row 2: Transaksi -->
        <h5 class="text-muted mb-3"><i class="fas fa-exchange-alt"></i> Transaksi</h5>
        <div class="row g-4 mb-4">

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-warning fs-2 mr-3">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ number_format($totalSampahDisetorkan, 2, ',', '.') }} Kg</h5>
                        <small class="text-muted">Total Sampah Disetor</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-secondary fs-2 mr-3">
                        <i class="fas fa-dolly"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ number_format($totalPenjualanSampah, 2, ',', '.') }} Kg</h5>
                        <small class="text-muted">Total Sampah Dijual</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-info fs-2 mr-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalTabunganNasabah, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Saldo Nasabah</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-dark fs-2 mr-3">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Hasil Penjualan</small>
                    </div>
                </div>
            </div>

        </div>

        <!-- Row 3: Transaksi Konversi Emas -->
        <h5 class="text-muted mb-3"><i class="fas fa-coins"></i> Transaksi Konversi Emas</h5>
        <div class="row g-4 mb-4">

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3 border-left-warning">
                    <div class="me-3 fs-2 mr-3" style="color: #d4a017;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Rp {{ number_format($totalRupiahDiKonversi, 0, ',', '.') }}</h5>
                        <small class="text-muted">Total Rupiah Dikonversi</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3 border-left-warning">
                    <div class="me-3 fs-2 mr-3" style="color: #d4a017;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ rtrim(rtrim(number_format($totalGoldExchanged, 4, ',', '.'), '0'), ',') }} g</h5>
                        <small class="text-muted">Total Emas Hasil Konversi</small>
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts Row 1 -->
        <div class="row mt-4">

            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Setoran per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="setoranPenarikanChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Komposisi Jenis Sampah</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="komposisiSampahChart" height="300"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts Row 2 -->
        <div class="row">

            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nasabah Baru per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="nasabahBaruChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Penukaran Emas per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="goldExchangeChart" height="250"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Setoran per Bulan (Bar Chart)
        var setoranData = @json($setoranPerBulan);

        var setoranLabels = setoranData.map(i => i.bulan);
        var setoranValues = setoranData.map(i => parseFloat(i.total));

        new Chart(document.getElementById('setoranPenarikanChart'), {
            type: 'bar',
            data: {
                labels: setoranLabels,
                datasets: [{
                    label: 'Setoran (Rp)',
                    data: setoranValues,
                    backgroundColor: 'rgba(78, 115, 223, 0.7)',
                    borderColor: 'rgba(78, 115, 223, 1)',
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

        // Komposisi Jenis Sampah (Doughnut)
        var komposisiData = @json($komposisiSampah);
        var komposisiColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'
        ];

        new Chart(document.getElementById('komposisiSampahChart'), {
            type: 'doughnut',
            data: {
                labels: komposisiData.map(i => i.nama_jenis),
                datasets: [{
                    data: komposisiData.map(i => parseFloat(i.total_berat)),
                    backgroundColor: komposisiColors.slice(0, komposisiData.length),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12
                        }
                    }
                }
            }
        });

        // Nasabah Baru per Bulan (Bar Chart)
        var nasabahData = @json($nasabahBaruPerBulan);

        new Chart(document.getElementById('nasabahBaruChart'), {
            type: 'bar',
            data: {
                labels: nasabahData.map(i => i.bulan),
                datasets: [{
                    label: 'Nasabah Baru',
                    data: nasabahData.map(i => parseInt(i.total)),
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
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
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Penukaran Emas per Bulan (Bar Chart)
        var goldData = @json($goldPerBulan);

        new Chart(document.getElementById('goldExchangeChart'), {
            type: 'bar',
            data: {
                labels: goldData.map(i => i.bulan),
                datasets: [{
                    label: 'Emas (gram)',
                    data: goldData.map(i => parseFloat(i.total_gram)),
                    backgroundColor: 'rgba(212, 160, 23, 0.7)',
                    borderColor: 'rgba(212, 160, 23, 1)',
                    borderWidth: 1
                }, {
                    label: 'Rupiah Dikonversi',
                    data: goldData.map(i => parseFloat(i.total_saldo)),
                    backgroundColor: 'rgba(78, 115, 223, 0.5)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
@endsection
