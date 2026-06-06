@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-upload text-primary"></i> Data Setor Sampah</h1>

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
    <form method="GET" action="{{ route('admin.setoran.index') }}" class="row mb-3">
        <div class="col-md-2">
            <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                value="{{ request('nasabah') }}">
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
            <a href="{{ route('admin.setoran.index') }}" class="btn btn-secondary mr-5">Reset</a>
        </div>
        <div class="col-md-3 d-flex justify-content-end">

            <button type="submit" name="action" value="cetak" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i>
                Cetak
                Laporan</button>
            <a href="{{ route('admin.setoran.create') }}" class="btn btn-primary"><i
                    class="fas fa-plus fa-sm text-white-50"></i> Tambah
                Setoran</a>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nasabah</th>
                    <th>Tanggal</th>
                    <th>Nama Sampah</th>
                    <th>Harga/Kg</th>
                    <th>Berat(Kg)</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($setorans as $setoran)
                    @if ($setoran->details->count() > 0)
                        @foreach ($setoran->details as $detail)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $setoran->details->count() }}">{{ $setoran->nasabah->nama }}</td>
                                    <td rowspan="{{ $setoran->details->count() }}">{{ $setoran->created_at->format('d-m-Y') }}</td>
                                @endif
                                <td>{{ $detail->sampah->nama_sampah }}</td>
                                <td>Rp{{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                                <td>{{ $detail->berat }}</td>
                                <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                @if ($loop->first)
                                    <td rowspan="{{ $setoran->details->count() }}">
                                        @if ($setoran->status == 'dibatalkan')
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-success">Berhasil</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $setoran->details->count() }}">
                                        @if ($setoran->status == 'berhasil')
                                            <button type="button" class="btn btn-sm btn-danger btn-void-setoran"
                                                data-id="{{ $setoran->id }}"
                                                data-nasabah="{{ $setoran->nasabah->nama }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $setoran->nasabah->nama }}</td>
                            <td>{{ $setoran->created_at->format('d-m-Y') }}</td>
                            <td colspan="4" class="text-center">Tidak ada detail setoran</td>
                            <td>
                                @if ($setoran->status == 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-success">Berhasil</span>
                                @endif
                            </td>
                            <td>
                                @if ($setoran->status == 'berhasil')
                                    <button type="button" class="btn btn-sm btn-danger btn-void-setoran"
                                        data-id="{{ $setoran->id }}"
                                        data-nasabah="{{ $setoran->nasabah->nama }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data setoran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Void Setoran --}}
    <div class="modal fade" id="voidModalSetoran" tabindex="-1" aria-labelledby="voidModalSetoranLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="formVoidSetoran">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="voidModalSetoranLabel">Batalkan Transaksi Setoran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan transaksi setoran atas nama <strong id="nasabahNameSetoran"></strong>?</p>
                        <div class="mb-3">
                            <label for="alasan_batal_setoran" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                            <textarea name="alasan_batal" id="alasan_batal_setoran" class="form-control" rows="3" required></textarea>
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
        const voidButtons = document.querySelectorAll('.btn-void-setoran');
        voidButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nasabah;
                document.getElementById('formVoidSetoran').action = '/admin/setoran/' + id + '/void';
                document.getElementById('nasabahNameSetoran').textContent = nama;
                var modal = new bootstrap.Modal(document.getElementById('voidModalSetoran'));
                modal.show();
            });
        });
    });
</script>
@endsection
