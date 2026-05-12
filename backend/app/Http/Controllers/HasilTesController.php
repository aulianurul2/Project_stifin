<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HasilTesController extends Controller
{
// Di HasilTesController.php
public function index(Request $request)
{
    $tab = $request->get('tab', 'kelola');

    $query = DB::table('hasiltes')
        // Gunakan leftJoin agar data hasiltes tidak hilang jika klien null
        ->leftJoin('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
        ->select('hasiltes.*', 'klien.nama', 'klien.no_hp');

    if ($tab == 'kelola') {
        // Gunakan status 'Proses' sesuai yang di-insert PendaftaranController
        $data = $query->where('hasiltes.status_tes', 'Proses')->get();
    } else {
        $data = $query->where('hasiltes.status_tes', 'Selesai')->get();
    }

    return view('hasil-tes', compact('data', 'tab'));
}

  public function update(Request $request, $id)
{
    $request->validate([
        'file_hasil' => 'required|mimes:pdf,jpg,png,jpeg|max:2048',
        'file_detail' => 'required|mimes:pdf,doc,docx|max:5120',
    ]);

    $data = [
        'status_tes' => 'Selesai',
        'tanggal' => now(),
        'updated_at' => now()
    ];

    if($request->hasFile('file_hasil')){
        $nama = time().'_sertifikat_'.$request->file('file_hasil')->getClientOriginalName();
        $request->file('file_hasil')->move(public_path('uploads/hasil'), $nama);
        $data['file_hasil']=$nama;
    }

    if($request->hasFile('file_detail')){
        $nama2 = time().'_detail_'.$request->file('file_detail')->getClientOriginalName();
        $request->file('file_detail')->move(public_path('uploads/hasil'), $nama2);
        $data['file_detail']=$nama2;
    }

    DB::table('hasiltes')->where('id_tes',$id)->update($data);

    return redirect('/hasil-tes?tab=riwayat')
        ->with('success','Upload berhasil');
}
}