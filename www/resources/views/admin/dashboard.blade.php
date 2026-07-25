@extends('layouts.app')

@section('title', 'Bank Sampah - Dashboard')

@section('content')
    <div class="container-fluid">

        {{-- ROW 1: Card Harga Emas (Full) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-left-warning border-0">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-warning rounded-circle p-3 text-white d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                    <i class="fas fa-coins" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="col">
                                <small class="text-muted text-uppercase" style="letter-spacing: 1px;">Harga Emas 24K / Gram</small>
                                @if ($goldPrice['price_per_gram'] > 0)
                                    <h2 class="font-weight-bold mb-0" style="font-size: 2rem; color: #b8860b;">
                                        Rp {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}
                                    </h2>
                                @else
                                    <h2 class="font-weight-bold mb-0 text-muted">Tidak tersedia</h2>
                                @endif
                            </div>
                            <div class="col-auto text-right">
                                @if (!empty($goldPrice['timestamp']))
                                    <small class="text-muted d-block"><i class="fas fa-sync-alt"></i> {{ $goldPrice['timestamp'] }}</small>
                                @endif
                                @if ($goldChangePercent !== null)
                                    <small class="{{ $goldChangePercent >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                        <i class="fas {{ $goldChangePercent >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                        {{ $goldChangePercent >= 0 ? '+' : '' }}{{ $goldChangePercent }}%
                                    </small>
                                    <small class="text-muted"> dari kemarin</small>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 2: 4 Card (Nasabah, Pengepul, Total Sampah, Total Saldo) --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-primary fs-2 mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $jumlahNasabah }}</h5>
                        <small class="text-muted">Nasabah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-success fs-2 mr-3">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $jumlahPengepul }}</h5>
                        <small class="text-muted">Pengepul</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm d-flex flex-row align-items-center p-3">
                    <div class="me-3 text-warning fs-2 mr-3">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ number_format($totalSampahDisetorkan, 2, ',', '.') }} Kg</h5>
                        <small class="text-muted">Sampah Disetor</small>
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
                        <small class="text-muted">Total Saldo</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3: Grafik Tren Harga Emas (Full) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tren Harga Emas</h6>
                    </div>
                    <div class="card-body">
                        <div class="btn-group btn-group-sm mb-3" role="group">
                            <button type="button" class="btn btn-outline-primary gold-trend-filter active" data-days="7">7 Hari</button>
                            <button type="button" class="btn btn-outline-primary gold-trend-filter" data-days="30">30 Hari</button>
                            <button type="button" class="btn btn-outline-primary gold-trend-filter" data-days="90">3 Bulan</button>
                            <button type="button" class="btn btn-outline-primary gold-trend-filter" data-days="365">1 Tahun</button>
                        </div>
                        <div id="goldTrendChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 4: Grid 60:40 — Setoran per Bulan + Komposisi Sampah --}}
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Setoran per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <div id="setoranChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Komposisi Kategori Sampah</h6>
                    </div>
                    <div class="card-body">
                        <div id="komposisiChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 5: Grid 50:50 — Nasabah Baru + Konversi Saldo --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nasabah Baru per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <div id="nasabahBaruChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Konversi Saldo ke Emas</h6>
                    </div>
                    <div class="card-body">
                        <div id="konversiChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Nasabah Terbaru --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Nasabah Terbaru</h6>
                <a href="{{ route('admin.nasabah.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>No. HP</th>
                                <th>Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($nasabahTerbaru as $nasabah)
                                <tr>
                                    <td>{{ $nasabah->nama }}</td>
                                    <td>{{ $nasabah->nik }}</td>
                                    <td>{{ $nasabah->no_hp ?? '-' }}</td>
                                    <td>{{ $nasabah->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada nasabah</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // =====================================================
        // 1. Grafik Tren Harga Emas (Line + Filter)
        // =====================================================
        var goldHistoryCategories = @json($goldHistoryCategories);
        var goldHistoryData = @json($goldHistoryData);

        var goldTrendChart = new ApexCharts(document.getElementById('goldTrendChart'), {
            chart: { type: 'line', height: 350, toolbar: { show: false } },
            series: [{ name: 'Harga Emas/gram', data: goldHistoryData }],
            xaxis: { categories: goldHistoryCategories, type: 'datetime' },
            yaxis: { labels: { formatter: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#d4a017'],
            tooltip: { y: { formatter: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } }
        });
        goldTrendChart.render();

        document.querySelectorAll('.gold-trend-filter').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.gold-trend-filter').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                var days = parseInt(this.getAttribute('data-days'));
                var total = goldHistoryData.length;
                var slice = Math.min(days, total);
                goldTrendChart.updateOptions({
                    xaxis: { categories: goldHistoryCategories.slice(-slice) },
                    series: [{ data: goldHistoryData.slice(-slice) }]
                });
            });
        });

        // =====================================================
        // 2. Grafik Setoran per Bulan (Bar Chart)
        // =====================================================
        var setoranData = @json($setoranPerBulan);
        new ApexCharts(document.getElementById('setoranChart'), {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            series: [{ name: 'Setoran (Rp)', data: setoranData.map(function(i) { return parseFloat(i.total); }) }],
            xaxis: { categories: setoranData.map(function(i) { return i.bulan; }) },
            yaxis: { labels: { formatter: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } },
            colors: ['#4e73df'],
            tooltip: { y: { formatter: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } }
        }).render();

        // =====================================================
        // 3. Grafik Komposisi Kategori Sampah (Donut)
        // =====================================================
        var komposisiData = @json($komposisiSampah);
        new ApexCharts(document.getElementById('komposisiChart'), {
            chart: { type: 'donut', height: 350 },
            series: komposisiData.map(function(i) { return parseFloat(i.total_berat); }),
            labels: komposisiData.map(function(i) { return i.nama_kategori; }),
            colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
            legend: { position: 'bottom' },
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 } } }]
        }).render();

        // =====================================================
        // 4. Grafik Nasabah Baru per Bulan
        // =====================================================
        var nasabahData = @json($nasabahBaruPerBulan);
        new ApexCharts(document.getElementById('nasabahBaruChart'), {
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            series: [{ name: 'Nasabah Baru', data: nasabahData.map(function(i) { return parseInt(i.total); }) }],
            xaxis: { categories: nasabahData.map(function(i) { return i.bulan; }) },
            yaxis: { labels: { formatter: function(v) { return Math.round(v); } } },
            colors: ['#1cc88a'],
            tooltip: { y: { formatter: function(v) { return v + ' nasabah'; } } }
        }).render();

        // =====================================================
        // 5. Grafik Konversi Saldo (Line + Area, Dual Y-Axis)
        // =====================================================
        var goldData = @json($goldPerBulan);
        var goldCategories = goldData.map(function(i) { return i.bulan; });
        var rupiahData = goldData.map(function(i) { return parseFloat(i.total_saldo); });
        var gramData = goldData.map(function(i) { return parseFloat(i.total_gram); });

        new ApexCharts(document.getElementById('konversiChart'), {
            chart: { type: 'line', height: 300, toolbar: { show: false } },
            series: [
                { name: 'Rupiah Dikonversi (Rp)', type: 'line', data: rupiahData },
                { name: 'Emas (gram)', type: 'area', data: gramData }
            ],
            xaxis: { categories: goldCategories },
            yaxis: [
                { title: { text: 'Rupiah (Rp)' }, labels: { formatter: function(v) { return 'Rp ' + v.toLocaleString('id-ID'); } } },
                { title: { text: 'Gram' }, opposite: true, labels: { formatter: function(v) { return v.toFixed(2) + ' g'; } } }
            ],
            colors: ['#4e73df', '#d4a017'],
            stroke: { width: [2, 0], curve: 'smooth' },
            fill: { opacity: [1, 0.3], type: ['solid', 'solid'] },
            tooltip: {
                shared: true,
                intersect: false,
                y: { formatter: function(v, { seriesIndex }) { return seriesIndex === 0 ? 'Rp ' + v.toLocaleString('id-ID') : v.toFixed(4) + ' g'; } }
            },
            legend: { position: 'bottom' }
        }).render();
    </script>
@endsection