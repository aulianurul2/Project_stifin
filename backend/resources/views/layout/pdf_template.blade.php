<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik STIFIn Expert Mobile</title>
    <style>
        @page { margin: 1.5cm 1.5cm 2cm 1.5cm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333333; line-height: 1.4; font-size: 13px; }
        .header-container { border-bottom: 3px solid #00AA5B; padding-bottom: 15px; margin-bottom: 25px; }
        .brand-section { float: left; width: 60%; }
        .brand-title { font-size: 24px; font-weight: bold; color: #00AA5B; margin: 0; }
        .brand-subtitle { font-size: 12px; color: #666666; margin: 3px 0 0 0; }
        .meta-section { float: right; width: 40%; text-align: right; font-size: 11px; color: #555555; margin-top: 8px; }
        .meta-section p { margin: 2px 0; }
        .clearfix { clear: both; }
        .document-title { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a2e; margin: 15px 0 5px 0; text-transform: uppercase; }
        .document-period { text-align: center; font-size: 13px; color: #666; margin: 0 0 25px 0; }
        .summary-wrapper { margin-bottom: 30px; width: 100%; }
        .card { float: left; width: 46%; background-color: #f8faf8; border-left: 4px solid #00AA5B; padding: 12px 15px; border-radius: 4px; }
        .card-income { float: right; width: 46%; background-color: #f5fafd; border-left: 4px solid #0288d1; padding: 12px 15px; border-radius: 4px; }
        .card-label { font-size: 10px; text-transform: uppercase; color: #666666; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 4px; }
        .card-value { font-size: 18px; font-weight: bold; color: #1a1a2e; margin: 0; }

        /* Tabel dengan border tegas di setiap sel */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 2px solid #00AA5B; }
        th {
            background-color: #00AA5B;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 9px 10px;
            border: 1px solid #007a40;
        }
        td {
            padding: 8px 10px;
            color: #444444;
            border: 1px solid #b2dfb2;
            font-size: 11px;
        }
        tr:nth-child(even) td { background-color: #f0faf3; }
        tr:nth-child(odd)  td { background-color: #ffffff; }

        tfoot td {
            border: 1px solid #00AA5B;
            background-color: #e8f5e9 !important;
        }

        .text-left   { text-align: left; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .footer-note {
            position: fixed; bottom: 0; left: 0; right: 0;
            text-align: center; font-size: 10px; color: #999999;
            border-top: 1px solid #eeeeee; padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="brand-section">
            <h1 class="brand-title">STIFIn Expert Mobile</h1>
            <p class="brand-subtitle">Sistem Informasi Manajemen STIFIn Subang</p>
        </div>
        <div class="meta-section">
            <p><strong>Periode:</strong> {{ $bulan }} {{ $tahun }}</p>
            <p><strong>Tanggal Cetak:</strong> {{ date('d/m/Y') }}</p>
            <p><strong>Waktu Cetak:</strong> {{ date('H:i') }} WIB</p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="document-title">Laporan Analisis & Statistik Klien</div>
    <div class="document-period">Periode: {{ $bulan }} {{ $tahun }}</div>

    <div class="summary-wrapper">
        <div class="card">
            <div class="card-label">Total Klien Terdaftar</div>
            <div class="card-value">{{ $totalKlien }} Orang</div>
        </div>
        <div class="card-income">
            <div class="card-label">Total Pendapatan {{ $bulan }} {{ $tahun }}</div>
            <div class="card-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%"  class="text-center">No</th>
                <th width="22%" class="text-left">Nama Klien</th>
                <th width="21%" class="text-left">Domisili</th>
                <th width="11%" class="text-center">Hasil STIFIn</th>
                <th width="10%" class="text-center">Status</th>
                <th width="18%" class="text-right">Biaya Tes</th>
                <th width="14%" class="text-center">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatLaporan as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left" style="font-weight: normal;">{{ $row->nama }}</td>
                <td class="text-left">{{ $row->domisili ?? '-' }}</td>
                <td class="text-center" style="font-weight: 700;">{{ $row->hasil_tes ?? '-' }}</td>
                <td class="text-center" style="color: #00AA5B; font-weight: bold;">Selesai</td>
                <td class="text-right">Rp {{ number_format($row->biaya_tes, 0, ',', '.') }}</td>
                <td class="text-center">{{ $row->tanggal ? date('d M Y', strtotime($row->tanggal)) : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="color: #999; padding: 20px;">
                    Tidak ada data untuk periode {{ $bulan }} {{ $tahun }}.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($riwayatLaporan->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" style="font-weight: bold; text-align: right; padding: 10px; border-top: 2px solid #00AA5B;">
                    Total Pendapatan
                </td>
                <td style="font-weight: bold; text-align: right; padding: 10px; border-top: 2px solid #00AA5B;">
                    Rp {{ number_format($riwayatLaporan->sum('biaya_tes'), 0, ',', '.') }}
                </td>
                <td style="border-top: 2px solid #00AA5B;"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh Sistem Informasi STIFIn Manajemen Subang Ciayumajakuning.
    </div>

</body>
</html>