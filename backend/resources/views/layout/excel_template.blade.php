<table>
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px; background-color: #00AA5B; color: #ffffff;">
                LAPORAN DATA KLIEN STIFIN
            </th>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0; width: 30px;">Nama Klien</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0; width: 20px;">Hasil STIFIn</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0; width: 20px;">Biaya Tes (Rp)</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0; width: 20px;">Tanggal Tes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayatLaporan as $row)
        <tr>
            <td style="border: 1px solid #000;">{{ $row->nama }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $row->hasil }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($row->biaya_tes, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #f9f9f9;">
            <td colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: right;">TOTAL PENDAPATAN</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: right;">
                {{ number_format($riwayatLaporan->sum('biaya_tes'), 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #000;"></td>
        </tr>
    </tfoot>
</table>