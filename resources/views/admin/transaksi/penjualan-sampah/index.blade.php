@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-cash-register text-primary"></i> Data Penjualan Sampah</h1>

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
    <form method="GET" action="{{ route('admin.penjualan.index') }}" class="row mb-3">
        <div class="col-md-2">
            <input type="text" name="pengepul" class="form-control" placeholder="Cari Nama Pengepul"
                value="{{ request('pengepul') }}">
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
        <div class="col md-3">
            <button type="submit" name="action" value="filter" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary mr-5">Reset</a>
        </div>
        <div class="col-md-3 d-flex justify-content-end">

            <button type="submit" name="action" value="cetak" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i>
                Cetak
                Laporan</button>
            <a href="{{ route('admin.penjualan.create') }}" class="btn btn-primary"><i
                    class="fas fa-plus fa-sm text-white-50"></i>
                Tambah
                Penjualan</a>
        </div>
    </form>




    <div class="table-responsive mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pengepul</th>
                    <th>Total Harga</th>
                    <th>Sampah</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualans as $jual)
                    <tr>
                        <td>{{ $jual->tanggal }}</td>
                        <td>{{ $jual->pengepul->nama }}</td>
                        <td>Rp {{ number_format($jual->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <ul>
                                @foreach ($jual->detail_penjualan as $detail)
                                    <li>{{ $detail->sampah->nama_sampah }} ({{ $detail->berat }} kg)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if ($jual->status == 'dibatalkan')
                                <span class="badge badge-danger">Dibatalkan</span>
                            @else
                                <span class="badge badge-success">Berhasil</span>
                            @endif
                        </td>
                        <td>
                            @if ($jual->status == 'berhasil')
                                <button type="button" class="btn btn-sm btn-danger btn-void-penjualan"
                                    data-id="{{ $jual->id }}"
                                    data-pengepul="{{ $jual->pengepul->nama }}">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data penjualan sampah.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Modal Void Penjualan --}}
    <div class="modal fade" id="voidModalPenjualan" tabindex="-1" aria-labelledby="voidModalPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="formVoidPenjualan">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="voidModalPenjualanLabel">Batalkan Transaksi Penjualan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan transaksi penjualan atas nama pengepul <strong id="pengepulNamePenjualan"></strong>?</p>
                        <div class="mb-3">
                            <label for="alasan_batal_penjualan" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                            <textarea name="alasan_batal" id="alasan_batal_penjualan" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const voidButtons = document.querySelectorAll('.btn-void-penjualan');
        voidButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.pengepul;
                document.getElementById('formVoidPenjualan').action = '/admin/penjualan/' + id + '/void';
                document.getElementById('pengepulNamePenjualan').textContent = nama;
                var modal = new bootstrap.Modal(document.getElementById('voidModalPenjualan'));
                modal.show();
            });
        });
    });
</script>
@endsection
