@extends('layouts.app')

@section('title', 'Data Sampah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Sampah</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Sampah yang Dapat Disetorkan</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Berikut adalah daftar jenis sampah yang dapat disetorkan ke bank sampah beserta harga per kilogramnya.</p>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kategori</th>
                            <th>Jenis Sampah</th>
                            <th>Harga/kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisSampahs as $index => $item)
                            <tr>
                                <td>{{ ($jenisSampahs->currentPage() - 1) * $jenisSampahs->perPage() + $index + 1 }}</td>
                                <td>{{ $item->kategoriSampah->nama_kategori ?? '-' }} <small class="text-muted">({{ $item->kategoriSampah->keterangan ?? '-' }})</small></td>
                                <td>{{ $item->nama_jenis }}</td>
                                <td>Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $jenisSampahs->links() }}
            </div>
        </div>
    </div>
@endsection
