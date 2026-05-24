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
        
        // 2. Pendaftaran: Menghitung slot jadwal yang SUDAH diisi oleh klien (bukan slot kosong)
        $pendaftaran = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->count(); 
        
        // 3. Hasil Tes: Diambil dari tabel hasiltes yang statusnya 'Selesai'
        $hasilTes = DB::table('hasiltes')->where('status_tes', 'Selesai')->count();
        
        // 4. Jadwal Terkini: Jadwal aktif yang sudah diisi klien, namun tesnya belum selesai
        $jadwalTerkini = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->where('status', '!=', 'Selesai')
            ->count();

        // --- LOGIKA GRAFIK ---
        $grafikData = DB::table('hasiltes')
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('count(*) as total'))
            ->whereYear('tanggal', '2026')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')->all();

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $grafikData[$i] ?? 0;
        }

        // --- AKTIVITAS TERBARU (PENDAFTARAN MASUK TERKINI) ---
        // Mengambil 5 slot jadwal terbaru yang sudah diisi oleh klien
        $aktivitasTerbaru = DB::table('jadwal')
            ->select('nama_klien', 'tanggal', 'status as status_tes')
            ->whereNotNull('nama_klien') // Pastikan hanya slot yang ada pendaftarnya
            ->orderBy('id_jadwal', 'desc') 
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