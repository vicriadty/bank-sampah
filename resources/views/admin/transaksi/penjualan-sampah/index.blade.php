@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Penjualan Sampah</h1>

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
            <a href="{{ route('admin.penjualan.create') }}" class="btn btn-primary"><i class="fas fa-plus fa-sm text-white-50"></i>
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
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualans as $jual)
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
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

@endsection
