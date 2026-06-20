@extends('layouts.app')

@section('title', 'Bank Sampah - Dashboard')

@section('content')
    <div class="container">

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

            <!-- Harga Emas Terkini -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3 border-left-warning">
                    <div class="me-3 fs-2 mr-3" style="color: #d4a017;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        @if ($goldPrice['price_per_gram'] > 0)
                            <h5 class="mb-0">Rp {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}/g</h5>
                        @else
                            <h5 class="mb-0">N/A</h5>
                        @endif
                        <small class="text-muted">Harga Emas 24K/gram</small>
                    </div>
                </div>
            </div>

            <!-- Total Emas Ditukar -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3 border-left-warning">
                    <div class="me-3 fs-2 mr-3" style="color: #d4a017;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ rtrim(rtrim(number_format($totalGoldExchanged, 4, ',', '.'), '0'), ',') }} g
                        </h5>
                        <small class="text-muted">Total Emas Ditukar</small>

                    </div>
                </div>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="row mt-4">

            <!-- Bar Chart: Setoran vs Penarikan per Bulan -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Setoran & Penarikan per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="setoranPenarikanChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart: Komposisi Jenis Sampah -->
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

        <div class="row">

            <!-- Line Chart: Nasabah Baru per Bulan -->
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

            <!-- Bar Chart: Penukaran Emas per Bulan -->
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

        // Nasabah Baru per Bulan (Line Chart)
        var nasabahData = @json($nasabahBaruPerBulan);

        new Chart(document.getElementById('nasabahBaruChart'), {
            type: 'line',
            data: {
                labels: nasabahData.map(i => i.bulan),
                datasets: [{
                    label: 'Nasabah Baru',
                    data: nasabahData.map(i => parseInt(i.total)),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
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
                                return value + ' g';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
