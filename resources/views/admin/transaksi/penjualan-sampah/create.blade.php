@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
    <div class="container">
        <h4>Form Penjualan Sampah ke Pengepul</h4>

        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            @method('POST')
            {{-- Select Pengepul --}}
            <div class="mb-3">
                <label for="pengepul_id" class="form-label">Pilih Pengepul</label>
                <select name="pengepul_id" id="pengepul_id" class="form-select" required>
                    <option value="">-- Pilih Pengepul --</option>
                    @foreach ($pengepuls as $pengepul)
                        <option value="{{ $pengepul->id }}">{{ $pengepul->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic Sampah Rows --}}
            <div id="sampah-container">
                <div class="row sampah-row mb-3">
                    <div class="col-md-5">
                        <label>Sampah</label>
                        <select name="sampah_id[]" class="form-select sampah-select" required>
                            <option value="">-- Pilih Sampah --</option>
                            @foreach ($sampahs as $sampah)
                                <option value="{{ $sampah->id }}" data-harga="{{ $sampah->harga_per_kg }}">
                                    {{ $sampah->nama_sampah }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Harga/kg</label>
                        <input type="text" class="form-control harga-per-kg" readonly>
                    </div>

                    <div class="col-md-3">
                        <label>Berat (kg)</label>
                        <input type="number" name="berat[]" class="form-control" step="0.01" required>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-remove-row">X</button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-row" class="btn btn-secondary mb-3">+ Tambah Sampah</button>
            <br>

            <button type="submit" class="btn btn-success">Simpan Penjualan</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Update harga/kg saat pilih sampah
        $(document).on('change', '.sampah-select', function() {
            const harga = $(this).find(':selected').data('harga');
            $(this).closest('.sampah-row').find('.harga-per-kg').val(harga);
        });

        // Tambah baris baru
        $('#add-row').click(function() {
            const row = $('.sampah-row').first().clone();
            row.find('select, input').val('');
            $('#sampah-container').append(row);
        });

        // Hapus baris
        $(document).on('click', '.btn-remove-row', function() {
            if ($('.sampah-row').length > 1) {
                $(this).closest('.sampah-row').remove();
            }
        });
    </script>
@endsection
