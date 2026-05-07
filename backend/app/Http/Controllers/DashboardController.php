<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Klien dari tabel klien
        $totalKlien = DB::table('klien')->count();
        
        // 2. Pendaftaran: Diambil dari tabel JADWAL (sesuai PendaftaranController kamu)
        $pendaftaran = DB::table('jadwal')->count(); 
        
        // 3. Hasil Tes: Diambil dari tabel HASILTES yang statusnya 'Selesai' (sesuai HasilTesController kamu)
        $hasilTes = DB::table('hasiltes')->where('status_tes', 'Selesai')->count();
        
        // 4. Jadwal Terkini: Bisa diambil dari data pendaftaran yang statusnya belum 'Selesai'
        $jadwalTerkini = DB::table('jadwal')->where('status', '!=', 'Selesai')->count();

        // --- LOGIKA GRAFIK ---
        // Mengambil data pertumbuhan dari tabel hasiltes berdasarkan kolom 'tanggal'
        $grafikData = DB::table('hasiltes')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('count(*) as total'))
            ->whereYear('tanggal', '2026')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')->all();

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $grafikData[$i] ?? 0;
        }

        // --- AKTIVITAS TERBARU ---
        // Kita gabungkan data terbaru dari tabel hasiltes
        $aktivitasTerbaru = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('klien.nama', 'hasiltes.tanggal', 'hasiltes.status_tes')
            ->latest('hasiltes.id_tes')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalKlien', 
            'pendaftaran', 
            'hasilTes', 
            'jadwalTerkini', 
            'aktivitasTerbaru',
            'dataBulanan'
        ));
    }
}