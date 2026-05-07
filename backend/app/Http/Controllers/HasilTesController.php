<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HasilTesController extends Controller
{
   public function index(Request $request)
{
    $tab = $request->get('tab', 'kelola');

    if ($tab == 'kelola') {
        // Tampilkan data yang baru diterima (belum selesai)
        $data = DB::table('hasiltes')
            ->where('status_tes', '!=', 'Selesai')
            ->get();
    } else {
        // Tampilkan riwayat yang sudah selesai
        $data = DB::table('hasiltes')
            ->where('status_tes', 'Selesai')
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