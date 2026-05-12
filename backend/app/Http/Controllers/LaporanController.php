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
        // 1. Total Klien yang terdaftar
        $totalKlien = DB::table('klien')->count();

        // 2. Total Tes yang sudah berstatus 'Selesai'
        $totalTesSelesai = DB::table('hasiltes')->where('status_tes', 'Selesai')->count();

        // 3. Total Pendapatan
        $totalPendapatan = DB::table('hasiltes')->where('status_tes', 'Selesai')->sum('biaya_tes');

        // 4. Statistik Distribusi (Sekarang diubah menghitung status saja karena tipe_tes dihapus)
        $statistikHasil = DB::table('hasiltes')
            ->select('status_tes as hasil', DB::raw('count(*) as total'))
            ->groupBy('status_tes')
            ->get();

        // 5. Ambil 10 Riwayat Tes Terbaru (JOIN dengan Klien untuk ambil Nama)
        $riwayatLaporan = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select(
                'klien.nama', 
                'hasiltes.status_tes as hasil', // Kita pakai status_tes sebagai pengganti 'hasil' di blade
                'hasiltes.tanggal'
            )
            ->where('hasiltes.status_tes', 'Selesai')
            ->orderBy('hasiltes.updated_at', 'desc')
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
        $data = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('klien.nama', 'hasiltes.status_tes as hasil', 'hasiltes.tanggal', 'hasiltes.biaya_tes')
            ->where('hasiltes.status_tes', 'Selesai')
            ->get();

        $pdf = Pdf::loadView('pdf_laporan', compact('data'));
    
         return $pdf->download('laporan-stifin.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new LaporanExport, 'laporan-stifin.xlsx');
    }
}