@extends('layouts.app')

@section('title', 'Riwayat Konversi Emas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Konversi Emas</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Konversi Saldo ke Emas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Saldo Terpakai (Rp)</th>
                            <th>Harga Emas / Gram</th>
                            <th>Jumlah Emas (g)</th>
                            <th>Sisa Saldo (Rp)</th>
                            <th>Total Saldo Emas (g)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($konversi as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>Rp{{ number_format($item->saldo_terpakai, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->harga_emas_per_gram, 0, ',', '.') }}</td>
                                <td>{{ rtrim(rtrim(number_format($item->jumlah_gram, 4, ',', '.'), '0'), ',') }}</td>
                                <td>Rp{{ number_format($item->sisa_saldo_rupiah, 0, ',', '.') }}</td>
                                <td>{{ rtrim(rtrim(number_format($item->total_saldo_emas, 4, ',', '.'), '0'), ',') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada konversi emas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
