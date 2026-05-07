<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        // Menampilkan semua slot jadwal yang dibuat oleh admin
        $jadwal = DB::table('jadwal')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        return view('jadwal-tes', compact('jadwal'));
    }

  public function store(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'waktu' => 'required',
        'kuota' => 'required|integer',
        'lokasi' => 'required' // Tambahkan validasi lokasi
    ]);

    DB::table('jadwal')->insert([
        'tanggal' => $request->tanggal,
        'waktu' => $request->waktu,
        'kuota' => $request->kuota,
        'lokasi' => $request->lokasi, // Simpan pilihan lokasi
        'status' => 'Tersedia',
        'created_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Slot jadwal berhasil diterbitkan!');
}

    public function destroy($id)
    {
        DB::table('jadwal')->where('id_jadwal', $id)->delete();
        return redirect()->back()->with('success', 'Slot jadwal berhasil dihapus');
    }

    // Fungsi tambahan untuk API React Native agar bisa mengambil jadwal
    public function getJadwalApi()
{
    // Ambil semua jadwal yang statusnya 'Tersedia'
    $data = DB::table('jadwal')
        ->where('status', 'Tersedia')
        ->get();
        
    return response()->json($data);
}
}