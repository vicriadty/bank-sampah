@extends('layouts.app')

@section('title', 'Tukar Saldo ke Emas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tukar Saldo ke Emas</h1>
    </div>

    @if (session('error'))
        <script>
            Swal.fire({
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endif

    <div class="row">
        <!-- Info Harga Emas -->
        <div class="col-lg-6">
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-coins"></i> Harga Emas Terkini</h6>
                </div>
                <div class="card-body">
                    @if ($goldPrice['price_per_gram'] > 0)
                        <div class="mb-3">
                            <small class="text-muted">Harga per gram (24K)</small>
                            <h4 class="font-weight-bold text-gray-800">Rp
                                {{ number_format($goldPrice['price_per_gram'], 0, ',', '.') }}</h4>
                        </div>
                        {{-- <div class="mb-3">
                        <small class="text-muted">Harga per troy ounce</small>
                        <p class="font-weight-bold text-gray-800">Rp {{ number_format($goldPrice['price_per_ounce'], 0, ',', '.') }}</p>
                    </div> --}}
                        <small class="text-muted"><i class="fas fa-clock"></i> Diperbarui:
                            {{ $goldPrice['timestamp'] }}</small>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Harga emas tidak tersedia saat ini. Silakan coba
                            lagi nanti.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Penukaran -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Penukaran Emas</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Saldo Aktif: Rp {{ number_format($nasabah->saldo, 0, ',', '.') }}</strong>
                        <br>
                        <small>* Minimal penukaran Rp 10.000</small>
                    </div>

                    @if ($goldPrice['price_per_gram'] > 0 && $nasabah->saldo >= 10000)
                        <form action="{{ route('nasabah.gold-exchange.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="jumlah_saldo">Jumlah Saldo yang Ditukarkan (Rp)</label>
                                <input type="number" name="jumlah_saldo" id="jumlah_saldo"
                                    class="form-control @error('jumlah_saldo') is-invalid @enderror"
                                    value="{{ old('jumlah_saldo') }}" required min="10000" max="{{ $nasabah->saldo }}">
                                @error('jumlah_saldo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Estimasi Emas yang Didapat</label>
                                <input type="text" id="estimasi_gram" class="form-control" readonly value="0 gram">
                            </div>

                            <div class="form-group">
                                <label for="catatan">Catatan (Opsional)</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-warning btn-block"
                                    onclick="return confirm('Apakah Anda yakin ingin menukarkan saldo ke emas?')">
                                    <i class="fas fa-exchange-alt"></i> Tukar ke Emas
                                </button>
                            </div>
                        </form>
                    @elseif($nasabah->saldo < 10000)
                        <div class="alert alert-danger mb-0">
                            Saldo Anda tidak mencukupi untuk melakukan penukaran emas. Minimal saldo Rp 10.000.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hargaPerGram = {{ $goldPrice['price_per_gram'] }};
            $('#jumlah_saldo').on('input', function() {
                var saldo = parseFloat($(this).val()) || 0;
                if (hargaPerGram > 0) {
                    var gram = saldo / hargaPerGram;
                    $('#estimasi_gram').val(gram.toFixed(4) + ' gram');
                }
            });
        });
    </script>
@endsection
