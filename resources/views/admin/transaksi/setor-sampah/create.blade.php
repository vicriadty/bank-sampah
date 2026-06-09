@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

@section('content')

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

    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Setoran Sampah</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.setoran.store') }}" method="post">
                        @csrf
                        @method('POST')
                        <div class="form-group mb-3">
                            <label>Nasabah</label>
                            <select name="nasabah_id" id="nasabah_id" class="form-control" required>
                                <option value="">Pilih Nasabah</option>
                                @foreach ($nasabahs as $nasabah)
                                    <option value="{{ $nasabah->id }}">{{ $nasabah->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Detail Sampah</h6>

                        {{-- Dynamic Sampah Rows --}}
                        <div id="sampah-container">
                            <div class="row sampah-row mb-3">
                                <div class="col-md-4">
                                    <label>Jenis Sampah</label>
                                    <select class="form-control jenis-sampah-select" required>
                                        <option value="">Pilih Jenis Sampah</option>
                                        @foreach ($jenisSampah as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Nama Sampah</label>
                                    <select name="sampah_id[]" class="form-control sampah-select" required>
                                        <option value="">Pilih Nama Sampah</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Berat (kg)</label>
                                    <input type="number" name="berat[]" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-remove-row mb-0" style="margin-bottom: 0 !important;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-row" class="btn btn-sm btn-info mb-3">+ Tambah Baris</button>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.setoran.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#nasabah_id').select2({
            placeholder: "Pilih Nasabah",
            allowClear: true,
            width: '100%'
        });

        // Load sampah when jenis_sampah changes
        $(document).on('change', '.jenis-sampah-select', function() {
            const row = $(this).closest('.sampah-row');
            const jenisID = $(this).val();
            const sampahSelect = row.find('.sampah-select');

            if (jenisID) {
                $.ajax({
                    url: '/admin/get-sampah-by-jenis/' + jenisID,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        sampahSelect.empty().append('<option value="">-- Pilih Nama Sampah --</option>');
                        $.each(data, function(key, value) {
                            sampahSelect.append(
                                '<option value="' + value.id + '">' +
                                value.nama_sampah + ' - Rp' + parseInt(value.harga_per_kg).toLocaleString() + '/kg' +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Gagal memuat nama sampah');
                    }
                });
            } else {
                sampahSelect.empty().append('<option value="">-- Pilih Nama Sampah --</option>');
            }
        });

        // Tambah baris baru
        $('#add-row').click(function() {
            const row = $('.sampah-row').first().clone();
            row.find('select, input').val('');
            row.find('.sampah-select').empty().append('<option value="">-- Pilih Nama Sampah --</option>');
            $('#sampah-container').append(row);
        });

        // Hapus baris
        $(document).on('click', '.btn-remove-row', function() {
            if ($('.sampah-row').length > 1) {
                $(this).closest('.sampah-row').remove();
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