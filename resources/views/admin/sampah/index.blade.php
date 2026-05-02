@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Sampah</h1>
        <a href="{{ route('admin.sampah.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Tambah Sampah
        </a>
    </div>

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

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Sampah</th>
                            <th>Nama Sampah</th>
                            <th>Harga per Kg</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sampah as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->jenisSampah->nama_jenis }}</td>
                                <td>{{ $item->nama_sampah }}</td>
                                <td>Rp{{ number_format($item->harga_per_kg, 0, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.sampah.edit', $item->id) }}"
                                            class="d-inline-block mr-2 btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <!-- Form hapus tersembunyi -->
                                        <form id="form-delete-{{ $item->id }}" action="{{ route('admin.sampah.destroy', $item->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
