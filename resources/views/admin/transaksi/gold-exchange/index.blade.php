@extends('layouts.app')

@section('title', 'Bank Sampah - Riwayat Konversi Emas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-history text-primary"></i> Riwayat Konversi Emas</h1>

        <div class="text-right">
            <button type="button" id="btnToggleSwitch"
                class="btn {{ $masterSwitch === '1' ? 'btn-success' : 'btn-danger' }}"
                style="min-width: 220px;">
                <i class="fas {{ $masterSwitch === '1' ? 'fa-check-circle' : 'fa-stop-circle' }}"></i>
                <strong>Master Switch: {{ $masterSwitch === '1' ? 'AKTIF' : 'MATI' }}</strong>
                <br>
                <small>
                    {{ $masterSwitch === '1' ? 'Status: Aktif berjalan otomatis' : 'Status: Dijeda / Sistem Dimatikan' }}
                </small>
            </button>

            <form id="formToggleSwitch" action="{{ route('admin.gold-exchange.toggle') }}" method="POST" style="display:none">
                @csrf
            </form>
        </div>

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
    </div>

    <form method="GET" action="{{ route('admin.gold-exchange.index') }}" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="nasabah" class="form-control" placeholder="Cari Nama Nasabah"
                value="{{ request('nasabah') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary mr-2">Filter</button>
            <a href="{{ route('admin.gold-exchange.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nasabah</th>
                    <th>Saldo Terpakai</th>
                    <th>Harga Emas/gram</th>
                    <th>Jumlah Gram</th>
                    <th>Sisa Saldo Rp</th>
                    <th>Total Saldo Emas</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    <tr>
                        <td>{{ $item->nasabah->nama }}</td>
                        <td>Rp{{ number_format($item->saldo_terpakai, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($item->harga_emas_per_gram, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->jumlah_gram, 4, ',', '.') }} g</td>
                        <td>Rp{{ number_format($item->sisa_saldo_rupiah, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->total_saldo_emas, 4, ',', '.') }} g</td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada riwayat konversi emas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $riwayat->links() }}
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#btnToggleSwitch').click(function() {
            let isOn = "{{ $masterSwitch }}" === '1';

            if (isOn) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Mematikan fitur ini akan menghentikan konversi emas otomatis untuk seluruh nasabah malam ini. Apakah Anda yakin ingin menjeda sistem?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Matikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formToggleSwitch').submit();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Aktifkan Auto-Convert?',
                    text: 'Sistem akan kembali mengkonversi saldo nasabah menjadi emas secara otomatis setiap malam.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Aktifkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formToggleSwitch').submit();
                    }
                });
            }
        });
    });
</script>
@endsection
