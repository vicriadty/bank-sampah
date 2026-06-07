@extends('layouts.app')

@section('title', 'Bank Sampah - Penukaran Emas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-coins text-primary"></i> Data Penukaran Emas</h1>

         {{-- Toast: Success --}}
            @if (session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '{{ session('success') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Toast: Error from session --}}
            @if (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: '{{ session('error') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                </script>
            @endif

            {{-- Toast: Validation errors --}}
            @if ($errors->any())
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                </script>
            @endif
    </div>

    {{-- Form Filter --}}
    <form method="GET" action="{{ route('admin.gold-exchange.index') }}" class="row mb-3">
        <div class="col-md-2">
            <input type="text" name="nasabah" class="form-control" placeholder="Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('admin.gold-exchange.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama Nasabah</th>
                    <th>Jumlah Saldo</th>
                    <th>Harga Emas/gram</th>
                    <th>Jumlah Gram</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($goldExchanges as $exchange)
                    <tr>
                        <td>{{ $exchange->nasabah->nama }}</td>
                        <td>Rp {{ number_format($exchange->jumlah_saldo, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($exchange->harga_emas_per_gram, 0, ',', '.') }}</td>
                        <td>{{ number_format($exchange->jumlah_gram, 4, ',', '.') }} g</td>
                        <td>
                            <span
                                class="badge
                                {{ $exchange->status == 'completed' ? 'badge-success' : '' }}
                                {{ $exchange->status == 'pending' ? 'badge-warning' : '' }}
                                {{ $exchange->status == 'rejected' ? 'badge-danger' : '' }}
                            ">
                                {{ ucfirst($exchange->status) }}
                            </span>
                        </td>
                        <td>{{ $exchange->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $goldExchanges->links() }}
        </div>
    </div>
@endsection

@endsection
