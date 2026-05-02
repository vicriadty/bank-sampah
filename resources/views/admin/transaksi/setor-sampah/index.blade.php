@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Setor Sampah</h1>

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
            <a href="{{ route('admin.setoran.create') }}" class="btn btn-primary"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah
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
                    <th>Harga /Kg</th>
                    <th>Berat</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($setorans as $setoran)
                    @if ($setoran->details->count() > 0)
                        @foreach ($setoran->details as $detail)
                            <tr>
                                <td>{{ $setoran->nasabah->nama }}</td>
                                <td>{{ $setoran->created_at->format('d-m-Y') }}</td>
                                <td>{{ $detail->sampah->nama_sampah }}</td>
                                <td>Rp{{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                                <td>{{ $detail->berat }}</td>
                                <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $setoran->nasabah->nama }}</td>
                            <td>{{ $setoran->created_at->format('d-m-Y') }}</td>
                            <td colspan="4" class="text-center">Tidak ada detail setoran</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data setoran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
