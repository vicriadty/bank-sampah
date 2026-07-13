@extends('layouts.app')

@section('title', 'Dashboard Nasabah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Nasabah</h1>
    </div>

    <div class="row">
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

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-left-warning border-0">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="bg-warning rounded-circle p-3 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                <i class="fas fa-coins" style="font-size: 1.3rem;"></i>
                            </div>
                        </div>
                        <div class="col">
                            <small class="text-muted text-uppercase" style="letter-spacing: 1px;">Harga Emas Terkini</small>
                            @if ($goldPrice['price_per_gram'] > 0)
                                <h4 class="font-weight-bold mb-0" style="color: #b8860b;">
                                    Rp {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}/gram
                                </h4>
                            @else
                                <h4 class="font-weight-bold mb-0 text-muted">Tidak tersedia</h4>
                            @endif
                        </div>
                        <div class="col-auto text-md-right">
                            <small class="text-muted">Estimasi Nilai Emas Anda</small>
                            @if ($goldPrice['price_per_gram'] > 0 && $saldoEmas > 0)
                                <h5 class="font-weight-bold mb-0 text-success">
                                    Rp {{ number_format($saldoEmas * $goldPrice['price_per_gram'], 0, ',', '.') }}
                                </h5>
                            @else
                                <h5 class="font-weight-bold mb-0 text-muted">-</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Setoran per Bulan</h6>
                </div>
                <div class="card-body">
                    <div id="setoranChart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Saldo Di Konversi (Rupiah ke Emas)</h6>
                </div>
                <div class="card-body">
                    <div id="konversiChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
            <a href="{{ route('nasabah.riwayat-transaksi') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Sampah</th>
                            <th>Berat</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerbaru as $setoran)
                            <tr>
                                <td>{{ $setoran->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @foreach ($setoran->details as $detail)
                                        {{ $detail->sampah->nama_jenis ?? '-' }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>{{ number_format($setoran->details->sum('berat'), 2, ',', '.') }} Kg</td>
                                <td>Rp {{ number_format($setoran->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var setoranData = @json($setoranPerBulan);
        new ApexCharts(document.getElementById('setoranChart'), {
            chart: { type: 'line', height: 250, toolbar: { show: false } },
            series: [{ name: 'Setoran (Rp)', data: setoranData.map(i => parseFloat(i.total)) }],
            xaxis: { categories: setoranData.map(i => i.bulan) },
            yaxis: { labels: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#4e73df'],
            tooltip: { y: { formatter: v => 'Rp ' + v.toLocaleString('id-ID') } }
        }).render();

        var konversiData = @json($konversiPerBulan);
        new ApexCharts(document.getElementById('konversiChart'), {
            chart: { type: 'line', height: 250, toolbar: { show: false } },
            series: [
                { name: 'Rupiah Dikonversi', data: konversiData.map(i => parseFloat(i.total_rupiah)) },
                { name: 'Emas (gram)', data: konversiData.map(i => parseFloat(i.total_gram)) }
            ],
            xaxis: { categories: konversiData.map(i => i.bulan) },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#1cc88a', '#d4a017'],
            tooltip: { y: { formatter: (v, { seriesIndex }) => seriesIndex === 0 ? 'Rp ' + v.toLocaleString('id-ID') : v.toFixed(4) + ' g' } }
        }).render();
    </script>
@endsection
