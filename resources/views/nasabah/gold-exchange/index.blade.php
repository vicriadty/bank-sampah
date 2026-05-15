@extends('layouts.app')

@section('title', 'Riwayat Penukaran Emas')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Penukaran Emas</h1>
    <a href="{{ route('nasabah.gold-exchange.create') }}" class="btn btn-warning btn-sm shadow-sm">
        <i class="fas fa-coins fa-sm text-white-50"></i> Tukar Emas Baru
    </a>
</div>

@if(session('success'))
    <script>
        Swal.fire({
            position: "top-end",
            text: "{{ session('success') }}",
            icon: "success",
            width: 600,
            showConfirmButton: false,
            timer: 1500
        });
    </script>
@endif
@if(session('error'))
    <script>
        Swal.fire({
            text: "{{ session('error') }}",
            icon: "error"
        });
    </script>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Penukaran Emas</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah Saldo</th>
                        <th>Harga Emas/gram</th>
                        <th>Jumlah Gram</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goldExchanges as $exchange)
                    <tr>
                        <td>{{ $exchange->created_at->format('d/m/Y H:i') }}</td>
                        <td>Rp {{ number_format($exchange->jumlah_saldo, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($exchange->harga_emas_per_gram, 0, ',', '.') }}</td>
                        <td>{{ number_format($exchange->jumlah_gram, 4, ',', '.') }} g</td>
                        <td>
                            <span class="badge
                                {{ $exchange->status == 'completed' ? 'badge-success' : '' }}
                                {{ $exchange->status == 'pending' ? 'badge-warning' : '' }}
                                {{ $exchange->status == 'rejected' ? 'badge-danger' : '' }}
                                {{ $exchange->status == 'approved' ? 'badge-info' : '' }}
                            ">
                                {{ ucfirst($exchange->status) }}
                            </span>
                        </td>
                        <td>{{ $exchange->catatan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada penukaran emas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $goldExchanges->links() }}
        </div>
    </div>
</div>
@endsection
