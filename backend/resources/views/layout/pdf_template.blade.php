<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Statistik STIFIn</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333333;
            line-height: 1.4;
            font-size: 13px;
        }
        
        /* Header Banner Area */
        .header-container {
            border-bottom: 3px solid #00AA5B;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .brand-section {
            float: left;
            width: 60%;
        }
        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #00AA5B;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 12px;
            color: #666666;
            margin: 3px 0 0 0;
        }
        .meta-section {
            float: right;
            width: 40%;
            text-align: right;
            font-size: 11px;
            color: #555555;
            margin-top: 8px;
        }
        .meta-section p {
            margin: 2px 0;
        }
        .clearfix {
            clear: both;
        }

        /* Title Document */
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a2e;
            margin: 15px 0 25px 0;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        
        /* Summary Cards Component */
        .summary-wrapper {
            margin-bottom: 30px;
            width: 100%;
        }
        .card {
            float: left;
            width: 46%;
            background-color: #f8faf8;
            border-left: 4px solid #00AA5B;
            padding: 12px 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-income {
            float: right;
            width: 46%;
            background-color: #f5fafd;
            border-left: 4px solid #0288d1;
            padding: 12px 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #666666;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .card-value {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a2e;
            margin: 0;
        }

        /* Modern Data Table */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        th { 
            background-color: #00AA5B; 
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border: 1px solid #00AA5B;
        }
        td { 
            border-bottom: 1px solid #e8f5e9; 
            padding: 10px 12px; 
            color: #444444;
        }
        tr:nth-child(even) td { 
            background-color: #fafdfa; 
        }
        
        /* Alignment and Helpers */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Footer Note */
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999999;
            border-top: 1px solid #eeeeee;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="brand-section">
            <h1 class="brand-title">STIFIn Mobile</h1>
            <p class="brand-subtitle">Sistem Informasi Manajemen STIFIn Subang</p>
        </div>
        <div class="meta-section">
            <p><strong>Tanggal Cetak:</strong> {{ date('d/m/Y') }}</p>
            <p><strong>Waktu Cetak:</strong> {{ date('H:i') }} WIB</p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="document-title">Laporan Analisis & Statistik Klien</div>

    <div class="summary-wrapper">
        <div class="card">
            <div class="card-label">Total Klien Terdaftar</div>
            <div class="card-value">{{ $totalKlien }} Orang</div>
        </div>
        <div class="card-income">
            <div class="card-label">Total Pendapatan (Omset)</div>
            <div class="card-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="45%" class="text-left">Nama Lengkap Klien</th>
                <th width="25%" class="text-center">Hasil Tes Genetik</th>
                <th width="25%" class="text-center">Tanggal Pelaksanaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatLaporan as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left" style="font-weight: 500;">{{ $row->nama }}</td>
                <td class="text-center">
                    <span style="color: #00AA5B; font-weight: bold;">{{ $row->hasil ?? 'Belum Rilis' }}</span>
                </td>
                <td class="text-center">{{ date('d M Y', strtotime($row->tanggal)) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #999; padding: 20px;">
                    Tidak ada data riwayat pendaftaran untuk laporan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh Sistem Informasi STIFIn Manajemen Subang Ciayumajakuning.
    </div>

</body>
</html>