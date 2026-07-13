@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-trash text-primary"></i> Data Sampah</h1>
        {{-- <a href="{{ route('admin.sampah.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Tambah Sampah
        </a> --}}
    </div>

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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSampah"><i
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
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kategoriSampah->nama_kategori }}</td>
                                <td>{{ $item->nama_jenis }}</td>
                                <td>Rp{{ number_format($item->harga_per_kg, 0, ',', '.') }}</td>
                                <td>{{ $item->stok }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('admin.sampah.edit', $item->id) }}"
                                            class="d-inline-block mr-2 btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="d-inline-block btn btn-danger btn-delete"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama_jenis }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <!-- Form hapus tersembunyi -->
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
                                <td colspan="5" class="text-center">Belum ada data sampah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Modal Tambah Sampah --}}
    <div class="modal fade" id="modalSampah" tabindex="-1" aria-labelledby="modalSampahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSampahLabel">Tambah Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSampah" action="{{ route('admin.sampah.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.sampah._form')
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

            // Submit form via AJAX
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
                        if (!res.ok) {
                            return res.json().then(data => Promise.reject(data));
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
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
                        let msg = err.error || (err.errors ? Object.values(err.errors).flat().join(
                            '<br>') : 'Terjadi kesalahan server');
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
    </script>
@endsection
