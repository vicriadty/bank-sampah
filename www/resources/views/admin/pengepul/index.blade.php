@extends('layouts.app')

@section('title', 'Bank Sampah - Pengepul')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-truck text-primary"></i> Data Pengepul</h1>

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

    <form method="GET" action="{{ route('admin.pengepul.index') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="pengepul" class="form-control" placeholder="Cari Nama Pengepul"
                value="{{ request('pengepul') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.pengepul.index') }}" class="btn btn-secondary">Reset</a>
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
                    <tbody>
                        @forelse ($pengepul as $item)
                            <tr>
                                <td>{{ ($pengepul->currentPage() - 1) * $pengepul->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->no_hp }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="d-inline-block mr-2 btn btn-sm btn-warning btn-edit-pengepul"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('admin.pengepul.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $pengepul->links() }}
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

    {{-- Modal Edit Pengepul --}}
    <div class="modal fade" id="modalEditPengepul" tabindex="-1" aria-labelledby="modalEditPengepulLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPengepulLabel">Ubah Pengepul</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPengepul" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="edit_nama">Nama Pengepul</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_alamat">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" cols="15" rows="4" class="form-control" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_no_hp">No. Handphone</label>
                            <input type="number" name="no_hp" id="edit_no_hp" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_status">Status</label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_keterangan">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" cols="15" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-update-pengepul" class="btn btn-warning">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Delete handler
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

        // Create handler
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
                if (!res.ok) return res.json().then(data => Promise.reject(data));
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalPengepul')).hide();
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

        // Edit modal — populate data
        const editButtons = document.querySelectorAll('.btn-edit-pengepul');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const form = document.getElementById('formEditPengepul');
                form.action = '{{ url("admin/pengepul") }}/' + id;

                fetch('{{ url("admin/pengepul") }}/' + id + '/edit-data', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_nama').value = data.nama || '';
                    document.getElementById('edit_alamat').value = data.alamat || '';
                    document.getElementById('edit_no_hp').value = data.no_hp || '';
                    document.getElementById('edit_status').value = data.status || '';
                    document.getElementById('edit_keterangan').value = data.keterangan || '';

                    var modal = new bootstrap.Modal(document.getElementById('modalEditPengepul'));
                    modal.show();
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Gagal memuat data pengepul' });
                });
            });
        });

        // Edit handler
        document.getElementById('btn-update-pengepul').addEventListener('click', function(e) {
            e.preventDefault();
            let form = document.getElementById('formEditPengepul');
            let data = new FormData(form);
            let nama = data.get('nama') || '-';

            Swal.fire({
                title: 'Konfirmasi Perubahan',
                html: `<div style="text-align: left;">
                    <p><strong>Nama:</strong> ${nama}</p>
                    <p style="margin-bottom:0">Apakah Anda yakin ingin menyimpan perubahan?</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                let btn = this;
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
                    if (!res.ok) return res.json().then(data => Promise.reject(data));
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalEditPengepul')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Pengepul berhasil diupdate!',
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
                    btn.textContent = 'Simpan Perubahan';
                });
            });
        });
    });
</script>
@endsection
