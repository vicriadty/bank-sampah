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

    </div>

    {{-- Form Filter --}}
    <form method="GET" action="{{ route('tarik-saldo.index') }}" class="row mb-3">
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
            <a href="{{ route('tarik-saldo.index') }}" class="btn btn-secondary mr-5">Reset</a>
        </div>
        <div class="col-md-3 d-flex justify-content-end">

            <button type="submit" name="action" value="cetak" class="btn btn-danger mr-2"><i class="fas fa-file-pdf"></i>
                Cetak
                Laporan</button>
            <a href="/tarik-saldo/create" class="btn btn-primary"><i class="fas fa-plus fa-sm text-white-50"></i> Tarik
                Saldo</a>
        </div>
    </form>

    {{-- Form Filter
    <form method="GET" action="{{ route('tarik-saldo.index') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        <div class="col-md-3 d-flex ">
            <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            <div class="ml-3 d-flex align-items-center">
                <span>s/d</span>
            </div>
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('tarik-saldo.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form> --}}

    {{-- Tabel Data --}}
    <div class="table-responsive mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Nasabah</th>
                    <th>Jumlah Tarik</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarikSaldos as $tarik)
                    <tr>
                        <td>{{ $tarik->nasabah->nama }}</td>
                        <td>Rp{{ number_format($tarik->jumlah_tarik, 0, ',', '.') }}</td>
                        <td>{{ $tarik->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
