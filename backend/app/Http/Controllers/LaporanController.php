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
    // 1. Ambil data untuk tabel
    $riwayatLaporan = DB::table('hasiltes')
        ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
        ->select('klien.nama', 'hasiltes.status_tes as hasil', 'hasiltes.tanggal')
        ->where('hasiltes.status_tes', 'Selesai')
        ->get();

    // 2. Ambil data ringkasan untuk header
    $totalKlien = DB::table('klien')->count();
    $totalPendapatan = DB::table('hasiltes')->where('status_tes', 'Selesai')->sum('biaya_tes');

    // 3. Masukkan ke dalam array data
    $data = [
        'riwayatLaporan' => $riwayatLaporan,
        'totalKlien'     => $totalKlien,
        'totalPendapatan'=> $totalPendapatan
    ];

    // 4. Panggil file template yang kita buat tadi (layout.pdf_template)
    $pdf = Pdf::loadView('layout.pdf_template', $data);

    // 5. Download file
    return $pdf->download('Laporan_STIFIn_' . date('d-m-Y') . '.pdf');
}

   public function exportExcel()
{
    // Menggunakan library Excel untuk memicu unduhan
    return Excel::download(new LaporanExport, 'Laporan_STIFIn_' . date('d-m-Y') . '.xlsx');
}
}