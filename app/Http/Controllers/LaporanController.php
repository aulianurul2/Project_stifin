<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
{
    // 1. Ambil Total Klien (Semua yang terdaftar)
    $totalKlien = DB::table('klien')->count();

    // 2. Ambil Total Tes Selesai (Semua waktu, bukan cuma bulan ini)
    $totalTesSelesai = DB::table('hasiltes')
                        ->where('status_tes', 'Selesai')
                        ->count();

    // 3. Ambil Total Pendapatan (Berdasarkan query sum kamu)
    $totalPendapatan = DB::table('hasiltes')
                        ->where('status_tes', 'Selesai')
                        ->sum('biaya_tes');

    // 4. Statistik Distribusi Hasil STIFIn (Untuk Progress Bar)
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

    // Kirim SEMUA variabel ke view
    return view('laporan', compact(
        'totalKlien', 
        'totalTesSelesai', 
        'totalPendapatan', 
        'statistikHasil', 
        'riwayatLaporan'
    ));
}
}