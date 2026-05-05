<!DOCTYPE html>
<html>
<head>
    <title>Laporan STIFIn</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN STATISTIK STIFIn</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y') }}</p>
    </div>

    <p>Total Klien: {{ $totalKlien }}</p>
    <p>Total Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Klien</th>
                <th>Hasil</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatLaporan as $row)
            <tr>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->hasil }}</td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>