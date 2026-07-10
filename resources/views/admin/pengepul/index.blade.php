@extends('layouts.app')

@section('title', 'Bank Sampah - Pengepul')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-truck text-primary"></i> Data Pengepul</h1>
        {{-- <a href="{{ route('admin.pengepul.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengepul</a> --}}


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


    <form method="GET" action="{{ route('admin.pengepul.search') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="pengepul" class="form-control" placeholder="Cari Nama Pengepul"
                value="{{ request('pengepul') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.pengepul.search') }}" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPengepul"><i
                    class="fas fa-plus fa-sm text-white-50"></i> Tambah
                Pengepul</button>
        </div>
    </form>

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
                                                data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
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

    {{-- Modal Tambah Pengepul --}}
    <div class="modal fade" id="modalPengepul" tabindex="-1" aria-labelledby="modalPengepulLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPengepulLabel">Tambah Pengepul</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPengepul" action="{{ route('admin.pengepul.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.pengepul._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const namaPengepul = this.getAttribute('data-nama');
                Swal.fire({
                    title: `Apakah Anda yakin ingin menghapus pengepul <br> ${namaPengepul}?`,
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

        document.getElementById('formPengepul').addEventListener('submit', function(e) {
            e.preventDefault();
            let form = this;
            let btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => Promise.reject(data));
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengepul berhasil ditambahkan!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => window.location.href = data.redirect);
                }
            })
            .catch(err => {
                let msg = err.error || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan server');
                Swal.fire({ icon: 'error', html: msg });
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Simpan';
            });
        });
    });
</script>
@endsection
