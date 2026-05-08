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
    </style>
</head>

<body>

    <h2 style="text-align: center;">Laporan Tarik Saldo</h2>

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

</body>

</html>
