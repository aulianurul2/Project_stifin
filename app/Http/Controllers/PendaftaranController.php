<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index()
    {
        // Mengambil data pendaftaran dari tabel jadwal
        $pendaftaran = DB::table('jadwal')->orderBy('id_jadwal', 'desc')->get();
        return view('pendaftaran-tes', compact('pendaftaran'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'komentar' => 'nullable|string'
        ]);

        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'status' => $request->status,
            'komentar' => $request->komentar
        ]);

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui');
    }
}