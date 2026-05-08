@extends('layouts.app')

@section('title', 'Bank Sampah - Pengepul')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pengepul</h1>
        <a href="{{ route('admin.pengepul.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengepul</a>


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


    <form method="GET" action="{{ route('admin.pengepul.search') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="pengepul" class="form-control" placeholder="Cari Nama Pengepul"
                value="{{ request('pengepul') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.pengepul.search') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pengepul</th>
                            <th>Alamat</th>
                            <th>No. Handphone</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    @if (count($pengepul) < 1)
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>
                    @else
                        <tbody>
                            @foreach ($pengepul as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                    <td>{{ $item->status }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.pengepul.edit', $item->id) }}"
                                                class="d-inline-block mr-2 btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                                data-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <!-- Form hapus tersembunyi -->
                                            <form id="form-delete-{{ $item->id }}"
                                                action="{{ route('admin.pengepul.destroy', $item->id) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif

                </table>
            </div>

        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-delete-' + id).submit();
                    }
                });
            });
        });
    });
</script>
