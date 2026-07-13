<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Setoran</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Laporan Setoran Sampah</h2>
    <table>
        <thead>
            <tr>
                <th>Nasabah</th>
                <th>Tanggal</th>
                <th>Nama Jenis</th>
                <th>Harga /Kg</th>
                <th>Berat</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($setorans as $setoran)
                @foreach ($setoran->details as $detail)
                    <tr>
                        <td>{{ $setoran->nasabah->nama }}</td>
                        <td>{{ $setoran->created_at->format('d-m-Y') }}</td>
                        <td>{{ $detail->sampah->nama_jenis }}</td>
                        <td>Rp{{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                        <td>{{ $detail->berat }}</td>
                        <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
