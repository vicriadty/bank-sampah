<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan Sampah</title>
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
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Laporan Penjualan Sampah</h3>
    <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pengepul</th>
                <th>Sampah</th>
                <th>Berat (kg)</th>
                <th>Harga/kg</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($penjualan_sampahs as $penjualan)
                @foreach ($penjualan->detail_penjualan as $detail)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $penjualan->pengepul->nama }}</td>
                        <td>{{ $detail->sampah->nama_jenis ?? '-' }}</td>
                        <td>{{ $detail->berat }}</td>
                        <td>Rp {{ number_format($detail->harga_per_kg, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
