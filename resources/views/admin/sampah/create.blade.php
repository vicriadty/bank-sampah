@extends('layouts.app')

@section('title', 'Bank Sampah - Sampah')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Sampah</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sampah Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sampah.store') }}" method="post">
                        @csrf
                        @method('POST')
                        <div class="form-group mb-3">
                            <label for="jenis_sampah_id">Jenis Sampah</label>
                            <select name="jenis_sampah_id" id="jenis_sampah_id"
                                class="form-control @error('jenis_sampah_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Sampah --</option>
                                @foreach ($jenisSampahs as $jenis)
                                    <option value="{{ $jenis->id }}" @selected(old('jenis_sampah_id') == $jenis->id)>
                                        {{ $jenis->nama_jenis }}</option>
                                @endforeach
                                <option value="__new__">+ Tambah Jenis Baru...</option>
                            </select>
                            @error('jenis_sampah_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama_sampah">Nama Sampah</label>
                            <input type="text" name="nama_sampah" id="nama_sampah"
                                class="form-control 
                            @error('nama_sampah') is-invalid @enderror"
                                value="{{ old('nama_sampah') }}">
                            @error('nama_sampah')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="harga_per_kg">Harga per Kg</label>
                            <input type="number" name="harga_per_kg" id="harga_per_kg" step="0.01"
                                class="form-control @error('harga_per_kg') is-invalid @enderror"
                                value="{{ old('harga_per_kg') }}">
                            @error('harga_per_kg')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                </div>

                <div class="form-group row mt-4 mr-2">
                    <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                        <a href="{{ route('admin.sampah.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk dropdown jenis sampah
        $('#jenis_sampah_id').select2({
            placeholder: "-- Pilih Jenis Sampah --",
            allowClear: true,
            width: '100%'
        });

        // Tangani pilihan "Tambah Jenis Baru"
        $('#jenis_sampah_id').on('change', function() {
            const val = $(this).val();
            if (val === '__new__') {
                Swal.fire({
                    title: 'Tambah Jenis Baru',
                    text: 'Masukkan nama jenis sampah baru:',
                    input: 'text',
                    inputPlaceholder: 'Nama jenis sampah...',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    preConfirm: (value) => {
                        if (!value) {
                            Swal.showValidationMessage('Nama jenis sampah wajib diisi');
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.jenis-sampah.quick-create") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                nama_jenis: result.value
                            },
                            success: function(res) {
                                if (res.success) {
                                    // Tambah option baru dan pilih
                                    const newOption = new Option(res.nama_jenis, res.id, true, true);
                                    $('#jenis_sampah_id').append(newOption).trigger('change');
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Jenis "' + res.nama_jenis + '" berhasil ditambahkan.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message || 'Gagal menyimpan jenis sampah.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: msg
                                });
                                // Kembalikan ke pilihan sebelumnya
                                $('#jenis_sampah_id').val('').trigger('change');
                            }
                        });
                    } else {
                        // Kembalikan ke pilihan sebelumnya
                        $('#jenis_sampah_id').val('').trigger('change');
                    }
                });
            }
        });

        // Konfirmasi sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Yakin ingin menyimpan data ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection