@extends('layouts.app')

@section('title', 'Bank Sampah - Tarik Saldo')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Tarik Saldo</h1>

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
    <form method="GET" action="{{ route('admin.tarik-saldo.index') }}" class="row mb-3">
        <div class="col-md-1">
            <input type="text" name="nasabah" class="form-control" placeholder="Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        <div class="col-md-1">
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2 d-flex ">
            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
            <div class="ml-3 d-flex align-items-center">
                <span>s/d</span>
            </div>
        </div>
        <div class="col-md-2">
            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
        </div>
        <!-- <div class="col-md-2 d-flex ">
            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
            <div class="ml-2 mr-2 d-flex align-items-center">
                <span>s/d</span>
            </div>
            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
        </div> -->
        <!-- <div class="col-md-6 d-flex justify-content-end align-items-center">
            <button type="submit" name="action" value="filter" class="btn btn-primary btn-sm mr-2">Filter</button>
            <a href="{{ route('admin.tarik-saldo.index') }}" class="btn btn-secondary btn-sm mr-2">Reset</a>
            <button type="submit" name="action" value="cetak" class="btn btn-danger btn-sm mr-2"><i class="fas fa-file-pdf"></i> Cetak</button>
            <a href="{{ route('admin.tarik-saldo.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tarik Saldo</a>
        </div> -->
         <div class="col md-3">
            <button type="submit" name="action" value="filter" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('admin.tarik-saldo.index') }}" class="btn btn-secondary mr-5">Reset</a>
        </div>
        <div class="col-md-3 d-flex justify-content-end">

            <button type="submit" name="action" value="cetak" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i>
                Cetak
                Laporan</button>
            <a href="{{ route('admin.tarik-saldo.create') }}" class="btn btn-primary"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah
                Tarik Saldo</a>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nama Nasabah</th>
                    <th>Jumlah Tarik</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarikSaldos as $tarik)
                    <tr>
                        <td>{{ $tarik->nasabah->nama }}</td>
                        <td>Rp {{ number_format($tarik->jumlah_tarik, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $tarik->status == 'approved' ? 'badge-success' : ($tarik->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($tarik->status) }}
                            </span>
                        </td>
                        <td>{{ $tarik->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($tarik->status == 'pending')
                                <div class="d-flex">
                                    <form action="{{ route('admin.tarik-saldo.approve', $tarik->id) }}" method="POST" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui penarikan ini?')">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $tarik->id }}">Reject</button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $tarik->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.tarik-saldo.reject', $tarik->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Permintaan</h5>
                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Alasan Penolakan</label>
                                                        <textarea name="keterangan" class="form-control" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                                    <button class="btn btn-danger" type="submit">Tolak</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $tarikSaldos->links() }}
        </div>
    </div>
@endsection
