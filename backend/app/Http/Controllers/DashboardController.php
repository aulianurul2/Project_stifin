<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = date('m');
        $tahunIni = date('Y');

        // 1. Total Klien keseluruhan (tidak difilter bulan)
        $totalKlien = DB::table('klien')->count();

        // 2. Pendaftaran bulan ini
        $pendaftaran = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->count();

        // 3. Hasil Tes selesai bulan ini
        $hasilTes = DB::table('hasiltes')
            ->where('status_tes', 'Selesai')
            ->whereMonth('updated_at', $bulanIni)
            ->whereYear('updated_at', $tahunIni)
            ->count();

        // 4. Jadwal aktif bulan ini yang belum selesai
        $jadwalTerkini = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->where('status', '!=', 'Selesai')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->count();

        // 5. Pendapatan bulan ini
        $pendapatanBulanIni = DB::table('hasiltes')
            ->where('status_tes', 'Selesai')
            ->whereMonth('updated_at', $bulanIni)
            ->whereYear('updated_at', $tahunIni)
            ->sum('biaya_tes');

        // 6. Grafik: data per bulan tahun berjalan
        $grafikData = DB::table('hasiltes')
            ->select(DB::raw('MONTH(updated_at) as bulan'), DB::raw('count(*) as total'))
            ->where('status_tes', 'Selesai')
            ->whereYear('updated_at', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')->all();

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $grafikData[$i] ?? 0;
        }

        // 7. Aktivitas terbaru bulan ini
        $aktivitasTerbaru = DB::table('jadwal')
            ->select('nama_klien', 'tanggal', 'status as status_tes')
            ->whereNotNull('nama_klien')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->orderBy('id_jadwal', 'desc')
            ->limit(5)
            ->get();

        // Label bulan Indonesia
        $namaBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
        $labelBulan = $namaBulan[(int)$bulanIni] . ' ' . $tahunIni;

        return view('dashboard', compact(
            'totalKlien',
            'pendaftaran',
            'hasilTes',
            'jadwalTerkini',
            'pendapatanBulanIni',
            'aktivitasTerbaru',
            'dataBulanan',
            'labelBulan'
        ));
    }
}