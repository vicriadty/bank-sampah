@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

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
                    <h6 class="m-0 font-weight-bold text-primary">POS Setoran Sampah</h6>
                </div>
                <div class="card-body">
                    <form id="formSetoran" action="{{ route('admin.setoran.store') }}" method="post">
                        @csrf
                        @method('POST')

                        @include('admin.transaksi.setor-sampah._form')

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 d-flex justify-content-end" style="gap: 10px;">
                                <a href="{{ route('admin.setoran.index') }}" class="btn btn-secondary">Batal</a>
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
            if (typeof $.fn.select2 !== 'undefined') {
                $('#nasabah_id').select2({
                    placeholder: "Pilih Nasabah",
                    allowClear: true,
                    width: '100%'
                });
            }

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

            function loadSampahByJenis(jenisSelect, sampahSelect, hargaInput) {
                let jenisID = jenisSelect.val();
                if (!jenisID) {
                    sampahSelect.empty().append('<option value="">Pilih Nama Sampah</option>');
                    hargaInput.val('');
                    return;
                }
                $.ajax({
                    url: '/admin/get-sampah-by-jenis/' + jenisID,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        sampahSelect.empty().append('<option value="">Pilih Nama Sampah</option>');
                        $.each(data, function(key, value) {
                            sampahSelect.append(
                                '<option value="' + value.id + '" data-harga="' + value
                                .harga_per_kg + '">' +
                                value.nama_jenis + ' - Rp' + parseInt(value.harga_per_kg)
                                .toLocaleString('id-ID') + '/kg' +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Gagal memuat nama sampah');
                    }
                });
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

            $(document).on('change', '.jenis-sampah-select', function() {
                let row = $(this).closest('.sampah-row');
                let sampahSelect = row.find('.sampah-select');
                let hargaInput = row.find('.harga-per-kg');
                loadSampahByJenis($(this), sampahSelect, hargaInput);
                row.find('.subtotal').val('');
                row.find('.subtotal').data('nilai', 0);
                hitungTotal();
            });

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

                let nasabah = $('#nasabah_id option:selected').text();
                let total = $('#total-harga').val();
                let items = $('.sampah-row').length;

                if (!$('#nasabah_id').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih nasabah terlebih dahulu'
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
                    title: 'Konfirmasi Setoran',
                    html: `
                    <div style="text-align: left;">
                        <p><strong>Nasabah:</strong> ${nasabah}</p>
                        <p><strong>Jenis Sampah:</strong> ${items} item</p>
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
                        url: $('#formSetoran').attr('action'),
                        method: 'POST',
                        data: $('#formSetoran').serialize(),
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Data Setoran berhasil disimpan!',
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
