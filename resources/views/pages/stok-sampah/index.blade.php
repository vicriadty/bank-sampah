@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Sampah Stock</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Jenis Sampah</th>
                    <th>Nama Sampah</th>
                    <th>Total Berat (kg)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sampahStock as $index => $stock)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $stock->sampah->jenis_sampah->nama_jenis ?? '-' }}</td>
                        <td>{{ $stock->sampah->nama_sampah ?? '-' }}</td>
                        <td>{{ number_format($stock->total_berat, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No stock available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
