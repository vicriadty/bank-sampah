@extends('layouts.app')

@section('title', 'Bank Sampah - Penukaran Emas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Penukaran Emas</h1>

        @if (session('success'))
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
        @if (session('error'))
            <script>
                Swal.fire({
                    text: "{{ session('error') }}",
                    icon: "error"
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
                    <th>Aksi</th>
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
                            <span class="badge
                                {{ $exchange->status == 'completed' ? 'badge-success' : '' }}
                                {{ $exchange->status == 'pending' ? 'badge-warning' : '' }}
                                {{ $exchange->status == 'rejected' ? 'badge-danger' : '' }}
                            ">
                                {{ ucfirst($exchange->status) }}
                            </span>
                        </td>
                        <td>{{ $exchange->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($exchange->status == 'pending')
                                <div class="d-flex">
                                    <form action="{{ route('admin.gold-exchange.approve', $exchange->id) }}" method="POST" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui penukaran emas ini?')">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm btn-reject-gold" 
                                        data-id="{{ $exchange->id }}" 
                                        data-url="{{ route('admin.gold-exchange.reject', $exchange->id) }}">
                                        Reject
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $goldExchanges->links() }}
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.btn-reject-gold', function() {
        const url = $(this).data('url');
        
        Swal.fire({
            title: 'Tolak Penukaran Emas',
            text: "Masukkan alasan penolakan:",
            input: 'textarea',
            inputPlaceholder: 'Tulis alasan di sini...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan wajib diisi!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const catatanInput = document.createElement('input');
                catatanInput.type = 'hidden';
                catatanInput.name = 'catatan';
                catatanInput.value = result.value;
                form.appendChild(catatanInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>
@endsection
