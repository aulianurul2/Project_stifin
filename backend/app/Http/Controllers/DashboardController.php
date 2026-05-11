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
        
        // 2. Pendaftaran: Total semua data di tabel jadwal
        $pendaftaran = DB::table('jadwal')->count(); 
        
        // 3. Hasil Tes: Diambil dari tabel hasiltes yang statusnya 'Selesai'
        $hasilTes = DB::table('hasiltes')->where('status_tes', 'Selesai')->count();
        
        // 4. Jadwal Terkini: Data di tabel jadwal yang statusnya belum 'Selesai'
        $jadwalTerkini = DB::table('jadwal')->where('status', '!=', 'Selesai')->count();

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

        // --- AKTIVITAS TERBARU (BAGIAN YANG DIPERBAIKI) ---
        $aktivitasTerbaru = DB::table('jadwal')
            ->join('klien', 'jadwal.id_klien', '=', 'klien.id_klien')
            ->select(
                'klien.nama as nama_klien', 
                'jadwal.created_at as tanggal', // MENGGUNAKAN created_at BUKAN tgl_pendaftaran
                'jadwal.status as status'
            )
            ->orderBy('jadwal.id_jadwal', 'desc')
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