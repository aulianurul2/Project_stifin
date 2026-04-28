<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HasilTesController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'kelola');

        if ($tab == 'riwayat') {
            // Data yang sudah selesai
            $data = DB::table('hasiltes')
                ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
                ->where('hasiltes.status_tes', 'Selesai')
                ->select('hasiltes.*', 'klien.nama', 'klien.no_hp')
                ->get();
        } else {
            // Data yang masih dalam proses/perlu diinput hasilnya
            $data = DB::table('hasiltes')
                ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
                ->where('hasiltes.status_tes', 'Proses')
                ->select('hasiltes.*', 'klien.nama', 'klien.no_hp')
                ->get();
        }

        return view('hasil-tes', compact('data', 'tab'));
    }

    public function update(Request $request, $id)
    {
        $file_name = null;
        if ($request->hasFile('file_hasil')) {
            $file_name = time() . '_' . $request->file('file_hasil')->getClientOriginalName();
            $request->file('file_hasil')->move(public_path('uploads/hasil'), $file_name);
        }

        DB::table('hasiltes')->where('id_tes', $id)->update([
            'hasil' => $request->hasil,
            'file_hasil' => $file_name,
            'status_tes' => 'Selesai',
            'tanggal' => now()
        ]);

        return redirect()->back()->with('success', 'Hasil tes berhasil disimpan!');
    }
}