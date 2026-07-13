@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
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

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">POS Penjualan Sampah</h6>
                </div>
                <div class="card-body">
                    <form id="formPenjualan" action="{{ route('admin.penjualan.store') }}" method="POST">
                        @csrf
                        @method('POST')

                        @include('admin.transaksi.penjualan-sampah._form')

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="button" id="btn-simpan" class="btn btn-primary">Simpan</button>
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
        $(document).ready(function() {
            function formatRupiah(angka) {
                return 'Rp' + parseInt(angka).toLocaleString('id-ID');
            }

            function hitungTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    let val = $(this).data('nilai') || 0;
                    total += val;
                });
                $('#total-harga').val(formatRupiah(total));
            }

            function updateSubtotal(row) {
                let harga = row.find('.sampah-select option:selected').data('harga') || 0;
                let berat = parseFloat(row.find('.berat-input').val()) || 0;
                let subtotal = harga * berat;
                row.find('.harga-per-kg').val(harga ? formatRupiah(harga) : '');
                row.find('.subtotal').val(subtotal ? formatRupiah(subtotal) : '');
                row.find('.subtotal').data('nilai', subtotal);
                hitungTotal();
            }

            $(document).on('change', '.sampah-select', function() {
                let row = $(this).closest('.sampah-row');
                updateSubtotal(row);
            });

            $(document).on('input', '.berat-input', function() {
                let row = $(this).closest('.sampah-row');
                updateSubtotal(row);
            });

            $('#add-row').click(function() {
                let row = $('.sampah-row').first().clone();
                row.find('select, input').val('');
                row.find('.subtotal').data('nilai', 0);
                row.find('.harga-per-kg, .subtotal').val('');
                $('#sampah-container').append(row);
            });

            $(document).on('click', '.btn-remove-row', function() {
                if ($('.sampah-row').length > 1) {
                    $(this).closest('.sampah-row').remove();
                    hitungTotal();
                }
            });

            $('#btn-simpan').click(function(e) {
                e.preventDefault();

                let pengepul = $('#pengepul_id option:selected').text();
                let total = $('#total-harga').val();
                let items = $('.sampah-row').length;

                if (!$('#pengepul_id').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih pengepul terlebih dahulu'
                    });
                    return;
                }

                let valid = true;
                $('.sampah-select').each(function() {
                    if (!$(this).val()) valid = false;
                });
                $('.berat-input').each(function() {
                    if (!$(this).val()) valid = false;
                });

                if (!valid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lengkapi semua item sampah'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penjualan',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Pengepul:</strong> ${pengepul}</p>
                            <p><strong>Item Sampah:</strong> ${items} item</p>
                            <p><strong>Total Harga:</strong> ${total}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    // Kirim via AJAX agar tidak reload & form tidak hilang
                    let $btn = $('#btn-simpan');
                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                    $.ajax({
                        url: $('#formPenjualan').attr('action'),
                        method: 'POST',
                        data: $('#formPenjualan').serialize(),
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Penjualan berhasil disimpan!',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => window.location.href = res.redirect);
                            }
                        },
                        error: function(xhr) {
                            // Tampilkan error tanpa me-reset form
                            let msg = xhr.responseJSON?.error ||
                                'Terjadi kesalahan server';
                            let errObj = xhr.responseJSON?.errors;
                            if (errObj) {
                                msg = Object.values(errObj).flat().join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                html: msg
                            });
                        },
                        complete: function() {
                            $btn.prop('disabled', false).text('Simpan');
                        }
                    });
                });
            });
        });
    </script>
@endsection
