<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Sampah</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        /* HEADER */
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

        /* TABLE */
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

        /* FOOTER */
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
                    Tanggal: {{ date('d-m-Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="sub-header">
        <h3>LAPORAN PENJUALAN SAMPAH</h3>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Pengepul</th>
                <th>Jenis Sampah</th>
                <th>Berat (kg)</th>
                <th>Harga/kg (Rp)</th>
                <th>Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHargaPerKg = 0;
                $totalBerat = 0;
                $totalSubtotal = 0;
            @endphp
            @foreach ($penjualans as $key => $penjualan)
                @foreach ($penjualan->detail_penjualan as $detail)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $penjualan->kode_penjualan ?? '-' }}</td>
                        <td>{{ $penjualan->pengepul->nama }}</td>
                        <td>{{ $detail->sampah->nama_jenis ?? '-' }}</td>
                        <td>{{ $detail->berat }}</td>
                        <td>{{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                        <td>{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $totalHargaPerKg += $detail->harga_per_kg;
                        $totalBerat += $detail->berat;
                        $totalSubtotal += $detail->subtotal;
                    @endphp
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ $totalBerat }}.00kg</strong></td>
                <td class="text-right"><strong>Rp{{ number_format($totalHargaPerKg, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp{{ number_format($totalSubtotal, 0, ',', '.') }}</strong></td>
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
