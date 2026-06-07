<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; font-size: 16px; background-color: #00AA5B; color: #ffffff;">
                LAPORAN DATA KLIEN STIFIN EXPERT MOBILE
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">No</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">Nama Klien</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">Domisili</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">Hasil STIFIn</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">Biaya Tes</th>
            <th style="font-weight: bold; border: 1px solid #000; background-color: #e0e0e0;">Tanggal Tes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayatLaporan as $i => $row)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $i + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $row->nama }}</td>
            <td style="border: 1px solid #000;">{{ $row->domisili ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $row->hasil_tes ?? '-' }}</td>
            {{-- Format Rp 600.000 tanpa desimal, pakai pemisah titik ribuan --}}
            <td style="border: 1px solid #000; text-align: right; mso-number-format:'\#\,\#\#0';">
                Rp {{ number_format($row->biaya_tes, 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">
                {{ $row->tanggal ? date('d/m/Y', strtotime($row->tanggal)) : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e8f5e9;">
                TOTAL PENDAPATAN
            </td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #e8f5e9;">
                Rp {{ number_format($riwayatLaporan->sum('biaya_tes'), 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #000; background-color: #e8f5e9;"></td>
        </tr>
    </tfoot>
</table>