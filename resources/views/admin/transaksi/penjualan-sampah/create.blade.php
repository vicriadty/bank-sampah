@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
    <div class="row">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penjualan Sampah</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.penjualan.store') }}" method="POST">
                        @csrf
                        @method('POST')
                        {{-- Select Pengepul --}}
                        <div class="mb-4">
                            <label for="pengepul_id" class="form-label font-weight-bold">Pilih Pengepul</label>
                            <select name="pengepul_id" id="pengepul_id" class="form-control" required>
                                <option value="">-- Pilih Pengepul --</option>
                                @foreach ($pengepuls as $pengepul)
                                    <option value="{{ $pengepul->id }}">{{ $pengepul->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Detail Sampah</h6>

                        {{-- Dynamic Sampah Rows --}}
                        <div id="sampah-container">
                            <div class="row sampah-row mb-3">
                                <div class="col-md-5">
                                    <label>Sampah</label>
                                    <select name="sampah_id[]" class="form-control sampah-select" required>
                                        <option value="">-- Pilih Sampah --</option>
                                        @foreach ($sampahs as $sampah)
                                            <option value="{{ $sampah->id }}" data-harga="{{ $sampah->harga_per_kg }}">
                                                {{ $sampah->nama_sampah }} - (Stok: {{ number_format($sampah->stok, 2) }} kg)
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
                                    <button type="button" class="btn btn-danger btn-remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-row" class="btn btn-sm btn-info mb-3">+ Tambah Baris</button>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
