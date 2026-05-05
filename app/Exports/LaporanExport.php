<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        // Mengambil data dari database menggunakan Query Builder
        $riwayatLaporan = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('klien.nama', 'hasiltes.hasil', 'hasiltes.biaya_tes', 'hasiltes.tanggal')
            ->orderBy('hasiltes.tanggal', 'desc')
            ->get();

        // Mengirim data ke file template di folder layout
        return view('layout.excel_template', compact('riwayatLaporan'));
    }
}