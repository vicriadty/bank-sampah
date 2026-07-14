@extends('layouts.app')

@section('title', 'Bank Sampah - Setoran')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-upload text-primary"></i> Data Setoran Sampah</h1>

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
    {{-- Form Filter --}}
    <form method="GET" action="{{ route('admin.setoran.index') }}" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
                <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                    value="{{ request('nasabah') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
            </div>
            <div class="col-12 col-md-4 d-flex flex-wrap gap-2">
                <button type="submit" name="action" value="filter" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.setoran.index') }}" class="btn btn-secondary">Reset</a>
                <button type="submit" name="action" value="cetak" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak Laporan</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSetoran"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Setoran</button>
            </div>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nasabah</th>
                    <th>Tanggal</th>
                    <th>Jenis Sampah</th>
                    <th>Harga/Kg</th>
                    <th>Berat(Kg)</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($setorans as $setoran)
                    @if ($setoran->details->count() > 0)
                        @foreach ($setoran->details as $detail)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $setoran->details->count() }}">{{ $setoran->kode_setoran ?? '-' }}</td>
                                    <td rowspan="{{ $setoran->details->count() }}">{{ $setoran->nasabah->nama }}</td>
                                    <td rowspan="{{ $setoran->details->count() }}">
                                        {{ $setoran->created_at->format('d-m-Y') }}</td>
                                @endif
                                <td>{{ $detail->sampah->nama_jenis }}</td>
                                <td>Rp{{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                                <td>{{ $detail->berat }}</td>
                                <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                @if ($loop->first)
                                    <td rowspan="{{ $setoran->details->count() }}">
                                        @if ($setoran->status == 'dibatalkan')
                                            <span class="badge badge-danger">Dibatalkan</span>
                                        @else
                                            <span class="badge badge-success">Berhasil</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $setoran->details->count() }}">
                                        @if ($setoran->status == 'berhasil')
                                            <button type="button" class="btn btn-sm btn-danger btn-void-setoran"
                                                data-id="{{ $setoran->id }}"
                                                data-nasabah="{{ $setoran->nasabah->nama }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $setoran->nasabah->nama }}</td>
                            <td>{{ $setoran->created_at->format('d-m-Y') }}</td>
                            <td colspan="4" class="text-center">Tidak ada detail setoran</td>
                            <td>
                                @if ($setoran->status == 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-success">Berhasil</span>
                                @endif
                            </td>
                            <td>
                                @if ($setoran->status == 'berhasil')
                                    <button type="button" class="btn btn-sm btn-danger btn-void-setoran"
                                        data-id="{{ $setoran->id }}" data-nasabah="{{ $setoran->nasabah->nama }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data setoran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $setorans->links() }}
    </div>

    {{-- Modal Void Setoran --}}
    <div class="modal fade" id="voidModalSetoran" tabindex="-1" aria-labelledby="voidModalSetoranLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="formVoidSetoran">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="voidModalSetoranLabel">Batalkan Transaksi Setoran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan transaksi setoran atas nama <strong
                                id="nasabahNameSetoran"></strong>?</p>
                        <div class="mb-3">
                            <label for="alasan_batal_setoran" class="form-label">Alasan Pembatalan <span
                                    class="text-danger">*</span></label>
                            <textarea name="alasan_batal" id="alasan_batal_setoran" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Setoran --}}
    <div class="modal fade" id="modalSetoran" tabindex="-1" aria-labelledby="modalSetoranLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSetoranLabel">POS Setoran Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSetoran" action="{{ route('admin.setoran.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.transaksi.setor-sampah._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-simpan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Void handler
            const voidButtons = document.querySelectorAll('.btn-void-setoran');
            voidButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nasabah;
                    document.getElementById('formVoidSetoran').action = '/admin/setoran/' + id +
                        '/void';
                    document.getElementById('nasabahNameSetoran').textContent = nama;
                    var modal = new bootstrap.Modal(document.getElementById('voidModalSetoran'));
                    modal.show();
                });
            });

            // Setoran form handlers
            function formatRupiah(angka) {
                return 'Rp' + parseInt(angka).toLocaleString('id-ID');
            }

            function hitungTotal() {
                let total = 0;
                document.querySelectorAll('.subtotal').forEach(el => {
                    let val = parseFloat(el.dataset.nilai) || 0;
                    total += val;
                });
                document.getElementById('total-harga').value = formatRupiah(total);
            }

            function loadSampahByJenis(jenisSelect, sampahSelect, hargaInput) {
                let jenisID = jenisSelect.value;
                if (!jenisID) {
                    sampahSelect.innerHTML = '<option value="">Pilih Nama Jenis</option>';
                    hargaInput.value = '';
                    return;
                }
                fetch('/admin/get-sampah-by-jenis/' + jenisID)
                    .then(res => res.json())
                    .then(data => {
                        sampahSelect.innerHTML = '<option value="">Pilih Nama Jenis</option>';
                        data.forEach(item => {
                            let opt = document.createElement('option');
                            opt.value = item.id;
                            opt.dataset.harga = item.harga_per_kg;
                            opt.textContent = item.nama_jenis + ' - Rp' + parseInt(item.harga_per_kg)
                                .toLocaleString('id-ID') + '/kg';
                            sampahSelect.appendChild(opt);
                        });
                    })
                    .catch(() => alert('Gagal memuat nama sampah'));
            }

            function updateSubtotal(row) {
                let sampahSelect = row.querySelector('.sampah-select');
                let hargaOption = sampahSelect.options[sampahSelect.selectedIndex];
                let harga = parseFloat(hargaOption?.dataset?.harga) || 0;
                let berat = parseFloat(row.querySelector('.berat-input').value) || 0;
                let subtotal = harga * berat;
                row.querySelector('.harga-per-kg').value = harga ? formatRupiah(harga) : '';
                let subEl = row.querySelector('.subtotal');
                subEl.value = subtotal ? formatRupiah(subtotal) : '';
                subEl.dataset.nilai = subtotal;
                hitungTotal();
            }

            // Init modal setoran form
            const modalSetoran = document.getElementById('modalSetoran');
            modalSetoran.addEventListener('shown.bs.modal', function() {
                // Select2
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#nasabah_id').select2({
                        placeholder: 'Pilih Nasabah',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#modalSetoran')
                    });
                }
            });

            modalSetoran.addEventListener('hidden.bs.modal', function() {
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#nasabah_id').select2('destroy');
                }
                document.getElementById('formSetoran').reset();
                let container = document.getElementById('sampah-container');
                container.innerHTML = '';
                let firstRow = document.querySelector('.sampah-row');
                if (firstRow) container.appendChild(firstRow.cloneNode(true));
                document.querySelectorAll('.sampah-row').forEach((row, i) => {
                    if (i > 0) row.remove();
                });
                document.querySelector('.sampah-row')?.querySelectorAll('select, input').forEach(el => el
                    .value = '');
                document.querySelector('.subtotal') && (document.querySelector('.subtotal').dataset.nilai =
                    0);
                document.querySelector('.harga-per-kg') && (document.querySelector('.harga-per-kg').value =
                    '');
                document.querySelector('.subtotal') && (document.querySelector('.subtotal').value = '');
                document.getElementById('total-harga').value = 'Rp0';
            });

            // Dynamic row events (delegated)
            document.getElementById('sampah-container').addEventListener('change', function(e) {
                if (e.target.classList.contains('jenis-sampah-select')) {
                    let row = e.target.closest('.sampah-row');
                    let sampahSelect = row.querySelector('.sampah-select');
                    let hargaInput = row.querySelector('.harga-per-kg');
                    loadSampahByJenis(e.target, sampahSelect, hargaInput);
                    row.querySelector('.subtotal').value = '';
                    row.querySelector('.subtotal').dataset.nilai = 0;
                    hitungTotal();
                }
                if (e.target.classList.contains('sampah-select')) {
                    updateSubtotal(e.target.closest('.sampah-row'));
                }
            });

            document.getElementById('sampah-container').addEventListener('input', function(e) {
                if (e.target.classList.contains('berat-input')) {
                    updateSubtotal(e.target.closest('.sampah-row'));
                }
            });

            document.getElementById('add-row').addEventListener('click', function() {
                let rows = document.querySelectorAll('.sampah-row');
                let row = rows[0].cloneNode(true);
                row.querySelectorAll('select, input').forEach(el => el.value = '');
                let subEl = row.querySelector('.subtotal');
                subEl.dataset.nilai = 0;
                row.querySelector('.harga-per-kg').value = '';
                subEl.value = '';
                document.getElementById('sampah-container').appendChild(row);
            });

            document.getElementById('sampah-container').addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-row')) {
                    let rows = document.querySelectorAll('.sampah-row');
                    if (rows.length > 1) {
                        e.target.closest('.sampah-row').remove();
                        hitungTotal();
                    }
                }
            });

            // Submit handler
            document.getElementById('btn-simpan').addEventListener('click', function(e) {
                e.preventDefault();

                let nasabahSelect = document.getElementById('nasabah_id');
                let nasabah = nasabahSelect.options[nasabahSelect.selectedIndex]?.text || '';
                let total = document.getElementById('total-harga').value;
                let items = document.querySelectorAll('.sampah-row').length;

                if (!nasabahSelect.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih nasabah terlebih dahulu'
                    });
                    return;
                }

                let valid = true;
                document.querySelectorAll('.sampah-select').forEach(el => {
                    if (!el.value) valid = false;
                });
                document.querySelectorAll('.berat-input').forEach(el => {
                    if (!el.value) valid = false;
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
                    html: `<div style="text-align: left;">
                    <p><strong>Nasabah:</strong> ${nasabah}</p>
                    <p><strong>Item Sampah:</strong> ${items} item</p>
                    <p><strong>Total Harga:</strong> ${total}</p>
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

                    fetch(document.getElementById('formSetoran').action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: new FormData(document.getElementById('formSetoran'))
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
                                    title: 'Data Setoran berhasil disimpan!',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => window.location.href = data.redirect);
                            }
                        })
                        .catch(err => {
                            let msg = err.error || (err.errors ? Object.values(err.errors)
                            .flat().join('<br>') : 'Terjadi kesalahan server');
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
        });
    </script>
@endsection
