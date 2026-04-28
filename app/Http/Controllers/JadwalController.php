<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        // Hanya mengambil data yang statusnya 'Diterima' (Jadwal Fix)
        $jadwal = DB::table('jadwal')
            ->where('status', 'Diterima')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        return view('jadwal-tes', compact('jadwal'));
    }

    public function destroy($id)
    {
        DB::table('jadwal')->where('id_jadwal', $id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dibatalkan');
    }
}