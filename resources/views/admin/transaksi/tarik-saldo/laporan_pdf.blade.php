<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Tarik Saldo</title>

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
        <h3>LAPORAN TARIK SALDO</h3>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Nasabah</th>
                <th>Tanggal</th>
                <th>Jumlah Tarik (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tarikSaldos as $key => $tarik)
                <tr>
                    <td style="width: 20px;">{{ $key + 1 }}</td>
                    <td>{{ $tarik->nasabah->nama }}</td>
                    <td>{{ \Carbon\Carbon::parse($tarik->created_at)->format('d-m-Y') }}</td>
                    <td>{{ number_format($tarik->jumlah_tarik, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right" width="150">
                    <strong>Rp {{ number_format($tarikSaldos->sum('jumlah_tarik'), 0, ',', '.') }}</strong>
                </td>
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
