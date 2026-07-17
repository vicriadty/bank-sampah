<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Konversi Emas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .header-left {
            text-align: left;
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
        }

        .header-right {
            text-align: right;
            font-size: 14px;
        }

        .sub-header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #0d6efd;
            color: white;
            padding: 8px;
            text-align: center;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .footer table {
            width: 100%;
        }

        .signature {
            text-align: right;
        }

        .signature-space {
            height: 60px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <table>
            <tr>
                <td class="header-left">
                    <img src="{{ public_path('icon/recycle.png') }}" width="20"> Bank Sampah
                </td>
                <td class="header-right">
                    Tanggal Cetak: {{ date('d-m-Y') }}
                    @if ($tanggalAwal && $tanggalAkhir)
                        <br>Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="sub-header">
        <h3>LAPORAN PENUKARAN EMAS</h3>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nasabah</th>
                <th>Saldo Terpakai (Rp)</th>
                <th>Harga Emas/gram (Rp)</th>
                <th>Jumlah Gram</th>
                <th>Sisa Saldo (Rp)</th>
                <th>Total Saldo Emas (g)</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSaldoTerpakai = 0;
                $totalGram = 0;
                $totalSisaSaldo = 0;
                $totalSaldoEmas = 0;
            @endphp
            @foreach ($riwayat as $key => $item)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $item->nasabah->nama }}</td>
                    <td class="text-right">{{ number_format($item->saldo_terpakai, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->harga_emas_per_gram, 0, ',', '.') }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->jumlah_gram, 4, ',', '.'), '0'), ',') }}</td>
                    <td class="text-right">{{ number_format($item->sisa_saldo_rupiah, 0, ',', '.') }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->total_saldo_emas, 4, ',', '.'), '0'), ',') }}</td>
                    <td class="text-center">{{ $item->created_at->format('d/m/Y') }}</td>
                </tr>
                @php
                    $totalSaldoTerpakai += $item->saldo_terpakai;
                    $totalGram += $item->jumlah_gram;
                    $totalSisaSaldo += $item->sisa_saldo_rupiah;
                    $totalSaldoEmas += $item->total_saldo_emas;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($totalSaldoTerpakai, 0, ',', '.') }}</strong></td>
                <td></td>
                <td class="text-right"><strong>{{ rtrim(rtrim(number_format($totalGram, 4, ',', '.'), '0'), ',') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalSisaSaldo, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ rtrim(rtrim(number_format($totalSaldoEmas, 4, ',', '.'), '0'), ',') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER / TTD -->
    <div class="footer">
        <table>
            <tr>
                <td></td>
                <td class="signature">
                    Mengetahui,<br>
                    Admin Bank Sampah
                    <div class="signature-space"></div>
                    <strong>(___________________)</strong>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
