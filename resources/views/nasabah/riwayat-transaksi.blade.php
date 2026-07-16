@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Transaksi</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi Terakhir</h6>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Sampah</th>
                            <th>Berat</th>
                            <th>Total (Rp)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $setoran)
                            <tr>
                                <td>{{ $setoran->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @foreach ($setoran->details as $detail)
                                        {{ $detail->sampah->nama_jenis ?? '-' }}@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>{{ number_format($setoran->details->sum('berat'), 2, ',', '.') }} Kg</td>
                                <td>Rp {{ number_format($setoran->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    @if ($setoran->status === 'berhasil')
                                        <span class="badge badge-success">Berhasil</span>
                                    @else
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
