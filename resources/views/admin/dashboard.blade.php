@extends('layouts.app')

@section('title', 'Bank Sampah - Dashboard')

@section('content')
    <div class="container-fluid">

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
                            <div class="col-auto text-md-right">
                                @if (!empty($goldPrice['updated_at']))
                                    <small class="text-muted"><i class="fas fa-sync-alt"></i> {{ $goldPrice['updated_at'] }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Setoran per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <div id="setoranChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Komposisi Jenis Sampah</h6>
                    </div>
                    <div class="card-body">
                        <div id="komposisiChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Penukaran Emas - Rupiah</h6>
                    </div>
                    <div class="card-body">
                        <div id="goldRupiahChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Penukaran Emas - Gram</h6>
                    </div>
                    <div class="card-body">
                        <div id="goldGramChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nasabah Baru per Bulan</h6>
                    </div>
                    <div class="card-body">
                        <div id="nasabahBaruChart"></div>
                    </div>
                </div>
            </div>
        </div>

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
        var setoranData = @json($setoranPerBulan);
        new ApexCharts(document.getElementById('setoranChart'), {
            chart: { type: 'line', height: 300, toolbar: { show: false } },
            series: [{ name: 'Setoran (Rp)', data: setoranData.map(i => parseFloat(i.total)) }],
            xaxis: { categories: setoranData.map(i => i.bulan) },
            yaxis: { labels: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#4e73df'],
            tooltip: { y: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } }
        }).render();

        var komposisiData = @json($komposisiSampah);
        new ApexCharts(document.getElementById('komposisiChart'), {
            chart: { type: 'pie', height: 300 },
            series: komposisiData.map(i => parseFloat(i.total_berat)),
            labels: komposisiData.map(i => i.nama_kategori),
            colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
            legend: { position: 'bottom' },
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 } } }]
        }).render();

        var nasabahData = @json($nasabahBaruPerBulan);
        new ApexCharts(document.getElementById('nasabahBaruChart'), {
            chart: { type: 'line', height: 250, toolbar: { show: false } },
            series: [{ name: 'Nasabah Baru', data: nasabahData.map(i => parseInt(i.total)) }],
            xaxis: { categories: nasabahData.map(i => i.bulan) },
            yaxis: { labels: { formatter: v => Math.round(v) } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#1cc88a'],
            tooltip: { y: { formatter: v => v + ' nasabah' } }
        }).render();

        var goldData = @json($goldPerBulan);
        var goldCategories = goldData.map(i => i.bulan);
        var goldTooltip = {
            y: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') }
        };
        new ApexCharts(document.getElementById('goldRupiahChart'), {
            chart: { type: 'line', height: 250, group: 'gold-group', toolbar: { show: false } },
            series: [{ name: 'Rupiah Dikonversi', data: goldData.map(i => parseFloat(i.total_saldo)) }],
            xaxis: { categories: goldCategories },
            yaxis: { labels: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#4e73df'],
            tooltip: goldTooltip
        }).render();

        new ApexCharts(document.getElementById('goldGramChart'), {
            chart: { type: 'line', height: 250, group: 'gold-group', toolbar: { show: false } },
            series: [{ name: 'Emas (gram)', data: goldData.map(i => parseFloat(i.total_gram)) }],
            xaxis: { categories: goldCategories },
            yaxis: { labels: { formatter: v => v.toFixed(2) + ' g' } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#d4a017'],
            tooltip: { y: { formatter: v => v.toFixed(4) + ' g' } }
        }).render();
    </script>
@endsection
