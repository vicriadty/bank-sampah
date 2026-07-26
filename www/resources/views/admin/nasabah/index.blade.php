@extends('layouts.app')

@section('title', 'Bank Sampah - Nasabah')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-users text-primary"></i> Data Nasabah</h1>

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

    <form method="GET" action="{{ route('admin.nasabah.index') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.nasabah.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNasabah"><i
                    class="fas fa-plus fa-sm text-white-50"></i> Tambah
                Nasabah</button>
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
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>No. Handphone</th>
                            <th>Saldo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nasabah as $item)
                            <tr>
                                <td>{{ ($nasabah->currentPage() - 1) * $nasabah->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->nik }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->jenis_kelamin }}</td>
                                <td>{{ $item->tempat_lahir }}, {{ $item->tanggal_lahir }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>{{ $item->no_hp }}</td>
                                <td>Rp {{ number_format($item->dompet->saldo_rupiah ?? 0, 2, ',', '.') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <button type="button"
                                            class="d-inline-block mr-2 btn btn-sm btn-warning btn-edit-nasabah"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('admin.nasabah.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $nasabah->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Nasabah --}}
    <div class="modal fade" id="modalNasabah" tabindex="-1" aria-labelledby="modalNasabahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNasabahLabel">Tambah Nasabah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="formNasabah" action="{{ route('admin.nasabah.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.nasabah._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="btn-simpan-nasabah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Nasabah --}}
    <div class="modal fade" id="modalEditNasabah" tabindex="-1" aria-labelledby="modalEditNasabahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditNasabahLabel">Ubah Nasabah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="formEditNasabah" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_nik">NIK</label>
                                    <input type="number" inputmode="numeric" name="nik" id="edit_nik"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_nama">Nama Lengkap</label>
                                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_tempat_lahir">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="edit_alamat">Alamat</label>
                                    <textarea name="alamat" id="edit_alamat" cols="15" rows="4" class="form-control" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_no_hp">No. Handphone</label>
                                    <input type="number" name="no_hp" id="edit_no_hp" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="btn-update-nasabah" class="btn btn-warning">Simpan Perubahan</button>
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
                    const namaNasabah = this.getAttribute('data-nama');
                    Swal.fire({
                        title: `Apakah Anda yakin ingin menghapus nasabah <br> ${namaNasabah}?`,
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
            document.getElementById('btn-simpan-nasabah').addEventListener('click', function(e) {
                e.preventDefault();
                let form = document.getElementById('formNasabah');
                let data = new FormData(form);
                let nama = data.get('nama') || '-';

                Swal.fire({
                    title: 'Konfirmasi Data Nasabah',
                    html: `<div style="text-align: left;">
                    <p><strong>Nama:</strong> ${nama}</p>
                    <p style="margin-bottom:0">Data nasabah beserta akun login akan dibuat.</p>
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
                                $('#modalNasabah').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Nasabah & Akun Login berhasil dibuat!',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => window.location.href = data.redirect);
                            }
                        })
                        .catch(err => {
                            let msg = err.error || (err.errors ? Object.values(err.errors)
                            .flat().join('<br>') : 'Terjadi kesalahan server');
                            Swal.fire({
                                icon: 'error',
                                html: msg
                            });
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.textContent = 'Simpan';
                        });
                });
            });

            // Edit modal — populate data
            const editButtons = document.querySelectorAll('.btn-edit-nasabah');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('formEditNasabah');
                    form.action = '{{ url('admin/nasabah') }}/' + id;

                    fetch('{{ url('admin/nasabah') }}/' + id + '/edit-data', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('edit_nik').value = data.nik || '';
                            document.getElementById('edit_nama').value = data.nama || '';
                            document.getElementById('edit_jenis_kelamin').value = data
                                .jenis_kelamin || '';
                            document.getElementById('edit_tanggal_lahir').value = data
                                .tanggal_lahir || '';
                            document.getElementById('edit_tempat_lahir').value = data
                                .tempat_lahir || '';
                            document.getElementById('edit_alamat').value = data.alamat || '';
                            document.getElementById('edit_no_hp').value = data.no_hp || '';

                            $('#modalEditNasabah').modal('show');
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal memuat data nasabah'
                            });
                        });
                });
            });

            // Edit handler
            document.getElementById('btn-update-nasabah').addEventListener('click', function(e) {
                e.preventDefault();
                let form = document.getElementById('formEditNasabah');
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
                                $('#modalEditNasabah').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Nasabah berhasil diupdate!',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => window.location.href = data.redirect);
                            }
                        })
                        .catch(err => {
                            let msg = err.error || (err.errors ? Object.values(err.errors)
                            .flat().join('<br>') : 'Terjadi kesalahan server');
                            Swal.fire({
                                icon: 'error',
                                html: msg
                            });
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
