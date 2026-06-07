<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        // Daftar tahun dari data hasiltes
        $daftarTahun = DB::table('hasiltes')
            ->selectRaw('YEAR(updated_at) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (!in_array((int) date('Y'), $daftarTahun)) {
            array_unshift($daftarTahun, (int) date('Y'));
        }

        // Base query: tes selesai, filter bulan & tahun
        $baseQuery = DB::table('hasiltes')
            ->where('status_tes', 'Selesai')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun);

        // Total klien keseluruhan ( difilter bulan)
        $totalKlien = DB::table('klien')
    ->whereMonth('created_at', $bulan)
    ->whereYear('created_at', $tahun)
    ->count();

        // Tes selesai bulan ini
        $totalTesSelesai = (clone $baseQuery)->count();

        // Pendapatan bulan ini dari biaya_tes
        $totalPendapatan = (clone $baseQuery)->sum('biaya_tes');

        // Distribusi bulan ini
        $statistikHasil = (clone $baseQuery)
            ->select('status_tes as hasil', DB::raw('count(*) as total'))
            ->groupBy('status_tes')
            ->get();

        // 10 riwayat terbaru bulan ini
        $riwayatLaporan = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('klien.nama', 'klien.domisili', 'hasiltes.hasil_tes', 'hasiltes.status_tes as hasil', 'hasiltes.tanggal', 'hasiltes.biaya_tes')
            ->where('hasiltes.status_tes', 'Selesai')
            ->whereMonth('hasiltes.updated_at', $bulan)
            ->whereYear('hasiltes.updated_at', $tahun)
            ->orderBy('hasiltes.updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('laporan', compact(
            'totalKlien',
            'totalTesSelesai',
            'totalPendapatan',
            'statistikHasil',
            'riwayatLaporan',
            'bulan',
            'tahun',
            'daftarTahun'
        ));
    }

  public function exportPdf(Request $request)
{
    $bulan = $request->query('bulan', date('m'));
    $tahun = $request->query('tahun', date('Y'));

    $riwayatLaporan = DB::table('hasiltes')
        ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
        ->select('klien.nama', 'klien.domisili', 'hasiltes.hasil_tes', 'hasiltes.status_tes as hasil', 'hasiltes.tanggal', 'hasiltes.biaya_tes')
        ->where('hasiltes.status_tes', 'Selesai')
        ->whereMonth('hasiltes.updated_at', $bulan)
        ->whereYear('hasiltes.updated_at', $tahun)
        ->orderBy('hasiltes.updated_at', 'desc')
        ->get();

    $totalKlien = DB::table('klien')
    ->whereMonth('created_at', $bulan)
    ->whereYear('created_at', $tahun)
    ->count();
    $totalPendapatan = DB::table('hasiltes')
        ->where('status_tes', 'Selesai')
        ->whereMonth('updated_at', $bulan)
        ->whereYear('updated_at', $tahun)
        ->sum('biaya_tes');

    // Fix: cast ke int supaya Carbon tidak error
    $namaBulan = \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan, 1)
        ->locale('id')
        ->monthName;

    $data = [
        'riwayatLaporan'  => $riwayatLaporan,
        'totalKlien'      => $totalKlien,
        'totalPendapatan' => $totalPendapatan,
        'bulan'           => $namaBulan,
        'tahun'           => $tahun,
    ];

    $pdf = Pdf::loadView('layout.pdf_template', $data);

    return $pdf->download('Laporan_STIFIn_' . $namaBulan . '_' . $tahun . '.pdf');
}

    public function exportExcel(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        return Excel::download(
            new LaporanExport($bulan, $tahun),
            'Laporan_STIFIn_' . $bulan . '_' . $tahun . '.xlsx'
        );
    }
}