<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index()
    {
        // 1. Ambil Total Klien
        $totalKlien = DB::table('klien')->count();

        // 2. Ambil Total Tes Selesai
        $totalTesSelesai = DB::table('hasiltes')
                            ->where('status_tes', 'Selesai')
                            ->count();

        // 3. Ambil Total Pendapatan
        $totalPendapatan = DB::table('hasiltes')
                            ->where('status_tes', 'Selesai')
                            ->sum('biaya_tes');

        // 4. Statistik Distribusi Hasil STIFIn
        $statistikHasil = DB::table('hasiltes')
                            ->select('hasil', DB::raw('count(*) as total'))
                            ->whereNotNull('hasil')
                            ->where('status_tes', 'Selesai')
                            ->groupBy('hasil')
                            ->get();

        // 5. Riwayat 10 Transaksi Terbaru
        $riwayatLaporan = DB::table('hasiltes')
                            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
                            ->select('klien.nama', 'hasiltes.hasil', 'hasiltes.biaya_tes', 'hasiltes.tanggal')
                            ->orderBy('hasiltes.tanggal', 'desc')
                            ->limit(10)
                            ->get();

        return view('laporan', compact(
            'totalKlien', 
            'totalTesSelesai', 
            'totalPendapatan', 
            'statistikHasil', 
            'riwayatLaporan'
        ));
    }

    public function exportPdf() 
    {
        // PERBAIKAN: Gunakan DB::table agar konsisten dengan fungsi index() 
        // dan menghindari error "Class Not Found"
        $data = [
            'totalKlien' => DB::table('klien')->count(),
            'totalTesSelesai' => DB::table('hasiltes')->where('status_tes', 'Selesai')->count(),
            'totalPendapatan' => DB::table('hasiltes')->where('status_tes', 'Selesai')->sum('biaya_tes'),
            'riwayatLaporan' => DB::table('hasiltes')
                                ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
                                ->select('klien.nama', 'hasiltes.hasil', 'hasiltes.tanggal')
                                ->orderBy('hasiltes.tanggal', 'desc')
                                ->get(),
            'statistikHasil' => DB::table('hasiltes') // Tambahkan ini jika template PDF membutuhkannya
                                ->select('hasil', DB::raw('count(*) as total'))
                                ->whereNotNull('hasil')
                                ->where('status_tes', 'Selesai')
                                ->groupBy('hasil')
                                ->get()
        ];

        $pdf = Pdf::loadView('layout.pdf_template', $data);
        return $pdf->download('Laporan_STIFIn_'.date('Y-m-d').'.pdf');
    }

    public function exportExcel() 
    {
        return Excel::download(new LaporanExport, 'Laporan-STIFIn-'.date('d-m-Y').'.xlsx');
    }
}