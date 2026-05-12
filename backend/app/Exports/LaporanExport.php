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
        // PERBAIKAN: Hapus 'hasiltes.hasil' karena kolomnya sudah tidak ada
        $riwayatLaporan = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select(
                'klien.nama', 
                'hasiltes.status_tes as hasil', // Gunakan status_tes sebagai pengganti
                'hasiltes.biaya_tes', 
                'hasiltes.tanggal'
            )
            ->orderBy('hasiltes.tanggal', 'desc')
            ->get();

        // Pastikan file view ini ada: resources/views/layout/excel_template.blade.php
        return view('layout.excel_template', compact('riwayatLaporan'));
    }
}