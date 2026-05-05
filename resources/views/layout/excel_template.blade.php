<table>
    <thead>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000;">Nama Klien</th>
            <th style="font-weight: bold; border: 1px solid #000;">Hasil STIFIn</th>
            <th style="font-weight: bold; border: 1px solid #000;">Biaya Tes</th>
            <th style="font-weight: bold; border: 1px solid #000;">Tanggal Tes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayatLaporan as $row)
        <tr>
            <td style="border: 1px solid #000;">{{ $row->nama }}</td>
            <td style="border: 1px solid #000;">{{ $row->hasil }}</td>
            <td style="border: 1px solid #000;">{{ $row->biaya_tes }}</td>
            <td style="border: 1px solid #000;">{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>