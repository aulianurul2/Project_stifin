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
            ->paginate(10);

        return view('jadwal-tes', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'kuota' => 'required|integer',
            'lokasi' => 'required' 
        ]);

        DB::table('jadwal')->insert([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'kuota' => $request->kuota,
            'lokasi' => $request->lokasi, 
            'status' => 'Tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Slot jadwal berhasil diterbitkan!');
    }

    public function destroy($id)
    {
        DB::table('jadwal')->where('id_jadwal', $id)->delete();
        // Ikut menghapus data tes berkaitan jika slot dihapus paksa oleh admin
        DB::table('hasiltes')->where('id_jadwal', $id)->delete();
        
        return redirect()->back()->with('success', 'Slot jadwal berhasil dihapus');
    }

    
   /**
     * API Internal untuk mengambil daftar pendaftar berdasarkan ID Jadwal (Untuk Popup Modal)
     */
   public function getKlienByJadwal($id)
{
    // Mengambil baris jadwal tersebut jika nama_klien tidak null dan tidak kosong
    $jadwal = DB::table('jadwal')
        ->where('id_jadwal', $id)
        ->whereNotNull('nama_klien')
        ->where('nama_klien', '!=', '') // Tambahan antisipasi string kosong
        ->select(
            'nama_klien', 
            'no_hp', 
            'status as status_jadwal', 
            'komentar'
        )
        ->get();

    return response()->json($jadwal);
}

    // Fungsi tambahan untuk API React Native agar bisa mengambil jadwal yang kuotanya masih tersedia (> 0)
   public function getJadwalApi()
{
    $data = DB::table('jadwal')
        ->where('status', 'Tersedia')
        ->whereNull('nama_klien') // KRUSIAL: Hanya ambil slot yang belum ada nama kliennya
        ->orderBy('tanggal', 'asc')
        ->orderBy('waktu', 'asc')
        ->get()
        ->map(function ($item) {
            $item->waktu = \Carbon\Carbon::parse($item->waktu)->format('H:i');
            return $item;
        });
        
    return response()->json($data);
}
}