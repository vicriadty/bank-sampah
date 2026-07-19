@extends('layouts.app')

@section('title', 'Info Saldo')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Informasi Saldo</h1>
</div>

<div class="row">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Saldo Rupiah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nasabah->dompet->saldo_rupiah ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Saldo Emas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ rtrim(rtrim(number_format($nasabah->dompet->saldo_emas_gram ?? 0, 4, ',', '.'), '0'), ',') }} g
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $semuaTransaksi = $setorans->map(function ($s) {
        return [
            'created_at' => $s->created_at,
            'tipe' => 'Setoran',
            'keterangan' => 'Setoran Sampah',
            'jumlah' => $s->total_harga,
            'arah' => 'masuk',
        ];
    })->merge(
        $konversis->map(function ($k) {
            return [
                'created_at' => $k->created_at,
                'tipe' => 'Konversi Emas',
                'keterangan' => 'Konversi ke Emas',
                'jumlah' => $k->saldo_terpakai,
                'arah' => 'keluar',
            ];
        })
    )->sortByDesc('created_at')->values();
@endphp

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Detail Arus Kas</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaTransaksi as $trx)
                        <tr>
                            <td>{{ $trx['created_at']->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($trx['tipe'] === 'Setoran')
                                    <span class="badge badge-success">Setoran</span>
                                @else
                                    <span class="badge badge-warning">Konversi Emas</span>
                                @endif
                            </td>
                            <td>{{ $trx['keterangan'] }}</td>
                            <td class="font-weight-bold {{ $trx['arah'] === 'masuk' ? 'text-success' : 'text-danger' }}">
                                {{ $trx['arah'] === 'masuk' ? '+' : '-' }}Rp {{ number_format($trx['jumlah'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
