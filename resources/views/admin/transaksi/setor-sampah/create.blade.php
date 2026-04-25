@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
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
                        <div class="form-group mb-3">
                            <label>Jenis Sampah</label>
                            <select id="jenis_sampah" class="form-control" required>
                                <option value="">Pilih Jenis Sampah</option>
                                @foreach ($jenisSampah as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Nama Sampah</label>
                            <select name="sampah_id" id="sampah_id" class="form-control" required>
                                <option value="">Pilih Nama Sampah</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Berat (kg)</label>
                            <input type="number" step="0.01" name="berat" class="form-control" required>
                        </div>

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

{{-- @section('scripts') --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#nasabah_id').select2({
            placeholder: "Pilih Nasabah",
            allowClear: true,
            width: '100%'
        });
    });

    $(document).ready(function() {
        $('#jenis_sampah').on('change', function() {
            let jenisID = $(this).val();
            if (jenisID) {
                $.ajax({
                    url: '/admin/get-sampah-by-jenis/' + jenisID,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#sampah_id').empty().append(
                            '<option value="">-- Pilih Nama Sampah --</option>');
                        $.each(data, function(key, value) {
                            $('#sampah_id').append(
                                '<option value="' + value.id + '">' +
                                value.nama_sampah + ' - Rp' + parseInt(value
                                    .harga_per_kg).toLocaleString() + '/kg' +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Gagal memuat nama sampah');
                    }
                });
            } else {
                $('#sampah').empty().append('<option value="">-- Pilih Nama Sampah --</option>');
            }
        });
    });
</script>
{{-- @endsection --}}
