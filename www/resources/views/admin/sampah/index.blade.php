@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-trash text-primary"></i> Data Sampah</h1>
    </div>

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

    <form action="{{ route('admin.sampah.index') }}" method="GET" class="row mb-3">
        <div class="col-md-3">
            <select name="nama_kategori" id="nama_kategori" class="form-control">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategoriSampahs as $kategori)
                    <option value="{{ $kategori->nama_kategori }}"
                        {{ request('nama_kategori') == $kategori->nama_kategori ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.sampah.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalSampah"><i
                    class="fas fa-plus fa-sm text-white-50"></i> Tambah
                Sampah</button>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Jenis Sampah</th>
                            <th>Harga per Kg</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sampah as $index => $item)
                            <tr>
                                <td>{{ ($sampah->currentPage() - 1) * $sampah->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->kategoriSampah->nama_kategori }}</td>
                                <td>{{ $item->nama_jenis }}</td>
                                <td>Rp{{ number_format($item->harga_per_kg, 0, ',', '.') }}</td>
                                <td>{{ $item->stok }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="d-inline-block mr-2 btn btn-sm btn-warning btn-edit-sampah"
                                            data-id="{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama_jenis }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="form-delete-{{ $item->id }}"
                                            action="{{ route('admin.sampah.destroy', $item->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $sampah->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Tambah Sampah --}}
    <div class="modal fade" id="modalSampah" tabindex="-1" aria-labelledby="modalSampahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSampahLabel">Tambah Sampah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="formSampah" action="{{ route('admin.sampah.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.sampah._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Sampah --}}
    <div class="modal fade" id="modalEditSampah" tabindex="-1" aria-labelledby="modalEditSampahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSampahLabel">Ubah Sampah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="formEditSampah" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="edit_kategori_id">Kategori Sampah</label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriSampahs as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_nama_jenis">Jenis Sampah</label>
                            <input type="text" name="nama_jenis" id="edit_nama_jenis" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_harga_per_kg">Harga per Kg</label>
                            <input type="number" name="harga_per_kg" id="edit_harga_per_kg" step="0.01" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="btn-update-sampah" class="btn btn-warning">Simpan Perubahan</button>
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
                    const namaSampah = this.getAttribute('data-nama');
                    Swal.fire({
                        title: `Apakah Anda yakin ingin menghapus sampah <br> ${namaSampah}?`,
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
            document.getElementById('formSampah').addEventListener('submit', function(e) {
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
                            $('#modalSampah').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Data sampah berhasil ditambahkan!',
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
            const editButtons = document.querySelectorAll('.btn-edit-sampah');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('formEditSampah');
                    form.action = '{{ url("admin/sampah") }}/' + id;

                    fetch('{{ url("admin/sampah") }}/' + id + '/edit-data', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('edit_kategori_id').value = data.kategori_id || '';
                        document.getElementById('edit_nama_jenis').value = data.nama_jenis || '';
                        document.getElementById('edit_harga_per_kg').value = data.harga_per_kg || '';

                        $('#modalEditSampah').modal('show');
                    })
                    .catch(() => {
                        Swal.fire({ icon: 'error', title: 'Gagal memuat data sampah' });
                    });
                });
            });

            // Edit handler
            document.getElementById('btn-update-sampah').addEventListener('click', function(e) {
                e.preventDefault();
                let form = document.getElementById('formEditSampah');
                let data = new FormData(form);
                let nama = data.get('nama_jenis') || '-';

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    html: `<div style="text-align: left;">
                        <p><strong>Jenis:</strong> ${nama}</p>
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
                            $('#modalEditSampah').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Sampah berhasil diupdate!',
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
