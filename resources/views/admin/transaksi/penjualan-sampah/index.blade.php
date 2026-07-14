@extends('layouts.app')

@section('title', 'Bank Sampah - Penjualan')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-cash-register text-primary"></i> Data Penjualan Sampah</h1>

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
    <form method="GET" action="{{ route('admin.penjualan.index') }}" class="row mb-3">
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="text" name="pengepul" class="form-control" placeholder="Cari Nama Pengepul"
                value="{{ request('pengepul') }}">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <button type="submit" name="action" value="filter" class="btn btn-primary me-1">Filter</button>
            <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary">Reset</a>
        </div>
        <div class="col-md-3 text-md-end">
            <button type="submit" name="action" value="cetak" class="btn btn-danger me-1"><i class="fas fa-file-pdf"></i> Cetak Laporan</button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPenjualan"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Penjualan</button>
        </div>
    </form>




    <div class="table-responsive mt-5">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Pengepul</th>
                    <th>Total Harga</th>
                    <th>Nama Jenis</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjualans as $jual)
                    <tr>
                        <td>{{ $jual->kode_penjualan ?? '-' }}</td>
                        <td>{{ $jual->tanggal }}</td>
                        <td>{{ $jual->pengepul->nama }}</td>
                        <td>Rp {{ number_format($jual->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <ul>
                                @foreach ($jual->detail_penjualan as $detail)
                                    <li>{{ $detail->sampah->nama_jenis }} ({{ $detail->berat }} kg)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if ($jual->status == 'dibatalkan')
                                <span class="badge badge-danger">Dibatalkan</span>
                            @else
                                <span class="badge badge-success">Berhasil</span>
                            @endif
                        </td>
                        <td>
                            @if ($jual->status == 'berhasil')
                                <button type="button" class="btn btn-sm btn-danger btn-void-penjualan"
                                    data-id="{{ $jual->id }}"
                                    data-pengepul="{{ $jual->pengepul->nama }}">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data penjualan sampah.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $penjualans->links() }}
    </div>

    {{-- Modal Void Penjualan --}}
    <div class="modal fade" id="voidModalPenjualan" tabindex="-1" aria-labelledby="voidModalPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="formVoidPenjualan">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="voidModalPenjualanLabel">Batalkan Transaksi Penjualan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan transaksi penjualan atas nama pengepul <strong id="pengepulNamePenjualan"></strong>?</p>
                        <div class="mb-3">
                            <label for="alasan_batal_penjualan" class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                            <textarea name="alasan_batal" id="alasan_batal_penjualan" class="form-control" rows="3" required></textarea>
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

    {{-- Modal Tambah Penjualan --}}
    <div class="modal fade" id="modalPenjualan" tabindex="-1" aria-labelledby="modalPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPenjualanLabel">POS Penjualan Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPenjualan" action="{{ route('admin.penjualan.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @include('admin.transaksi.penjualan-sampah._form')
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
        const voidButtons = document.querySelectorAll('.btn-void-penjualan');
        voidButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.pengepul;
                document.getElementById('formVoidPenjualan').action = '/admin/penjualan/' + id + '/void';
                document.getElementById('pengepulNamePenjualan').textContent = nama;
                var modal = new bootstrap.Modal(document.getElementById('voidModalPenjualan'));
                modal.show();
            });
        });

        // Penjualan form handlers
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

        // Reset form on modal close
        document.getElementById('modalPenjualan').addEventListener('hidden.bs.modal', function() {
            document.getElementById('formPenjualan').reset();
            let container = document.getElementById('sampah-container');
            let rows = container.querySelectorAll('.sampah-row');
            rows.forEach((row, i) => { if (i > 0) row.remove(); });
            let firstRow = container.querySelector('.sampah-row');
            if (firstRow) {
                firstRow.querySelectorAll('select, input').forEach(el => el.value = '');
                firstRow.querySelector('.subtotal') && (firstRow.querySelector('.subtotal').dataset.nilai = 0);
                firstRow.querySelector('.harga-per-kg') && (firstRow.querySelector('.harga-per-kg').value = '');
                firstRow.querySelector('.subtotal') && (firstRow.querySelector('.subtotal').value = '');
            }
            document.getElementById('total-harga').value = 'Rp0';
        });

        // Dynamic row events (delegated)
        document.getElementById('sampah-container').addEventListener('change', function(e) {
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

            let pengepulSelect = document.getElementById('pengepul_id');
            let pengepul = pengepulSelect.options[pengepulSelect.selectedIndex]?.text || '';
            let total = document.getElementById('total-harga').value;
            let items = document.querySelectorAll('.sampah-row').length;

            if (!pengepulSelect.value) {
                Swal.fire({ icon: 'warning', title: 'Pilih pengepul terlebih dahulu' });
                return;
            }

            let valid = true;
            document.querySelectorAll('.sampah-select').forEach(el => { if (!el.value) valid = false; });
            document.querySelectorAll('.berat-input').forEach(el => { if (!el.value) valid = false; });

            if (!valid) {
                Swal.fire({ icon: 'warning', title: 'Lengkapi semua item sampah' });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penjualan',
                html: `<div style="text-align: left;">
                    <p><strong>Pengepul:</strong> ${pengepul}</p>
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

                fetch(document.getElementById('formPenjualan').action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(document.getElementById('formPenjualan'))
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
                            title: 'Data Penjualan berhasil disimpan!',
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
