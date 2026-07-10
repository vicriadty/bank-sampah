@extends('layouts.app')

@section('title', 'Bank Sampah - Nasabah')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-users text-primary"></i> Data Nasabah</h1>
        {{-- <a href="{{ route('admin.nasabah.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-plus fa-sm text-white-50"></i> Tambah Nasabah</a> --}}


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

    <form method="GET" action="{{ route('admin.nasabah.search') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        {{-- <div class="col-md-3 d-flex ">
            <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            <div class="ml-3 d-flex align-items-center">
                <span>s/d</span>
            </div>
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
        </div> --}}
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.nasabah.search') }}" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNasabah"><i
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
                    @if (count($nasabah) < 1)
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data</td>
                            </tr>
                        </tbody>
                    @else
                        <tbody>
                            @foreach ($nasabah as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nik }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->jenis_kelamin }}</td>
                                    <td>{{ $item->tempat_lahir }}, {{ $item->tanggal_lahir }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                    <td>Rp {{ number_format($item->dompet->saldo_rupiah ?? 0, 2, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('admin.nasabah.edit', $item->id) }}"
                                                class="d-inline-block mr-2 btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                                data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <!-- Form hapus tersembunyi -->
                                            <form id="form-delete-{{ $item->id }}"
                                                action="{{ route('admin.nasabah.destroy', $item->id) }}" method="POST"
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

    {{-- Modal Tambah Nasabah --}}
    <div class="modal fade" id="modalNasabah" tabindex="-1" aria-labelledby="modalNasabahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNasabahLabel">Tambah Nasabah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNasabah" action="{{ route('admin.nasabah.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.nasabah._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-simpan-nasabah" class="btn btn-primary">Simpan</button>
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
                    if (!res.ok) {
                        return res.json().then(data => Promise.reject(data));
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
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
                    let msg = err.error || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan server');
                    Swal.fire({ icon: 'error', html: msg });
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = 'Simpan';
                });
            });
        });
    });
</script>
@endsection
