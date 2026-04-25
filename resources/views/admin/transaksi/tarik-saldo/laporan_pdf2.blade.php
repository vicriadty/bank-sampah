<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Tarik Saldo</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .header-left {
            float: left;
            color: #0d6efd;
        }

        .header-right {
            float: right;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">♻️ Bank Sampah</div>
        <div class="header-right">Laporan Tarik Saldo</div>
        <div class="clearfix"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Nasabah</th>
                <th>Jumlah Tarik</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tarikSaldos as $key => $tarik)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $tarik->nasabah->nama }}</td>
                    <td>Rp{{ number_format($tarik->jumlah_tarik, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($tarik->created_at)->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
