<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Tambahkan baris ini
use Illuminate\Support\Facades\DB;

class LaporanExport implements FromView, ShouldAutoSize // Tambahkan interface ini
{
    public function view(): View
    {
        $riwayatLaporan = DB::table('hasiltes')
            ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
            ->select('klien.nama', 'hasiltes.status_tes as hasil', 'hasiltes.biaya_tes', 'hasiltes.tanggal')
            ->where('hasiltes.status_tes', 'Selesai')
            ->get();

        return view('layout.excel_template', [
            'riwayatLaporan' => $riwayatLaporan
        ]);
    }
}