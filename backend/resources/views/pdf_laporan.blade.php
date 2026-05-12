<!DOCTYPE html>
<html>
<head>
    <title>Laporan STIFIn</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>LAPORAN HASIL TES STIFIn</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Klien</th>
                <th>Tanggal</th>
                <th>Hasil (Status)</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->nama }}</td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                <td>{{ $row->hasil }}</td>
                <td>Rp {{ number_format($row->biaya_tes, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>