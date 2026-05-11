<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    // Tampilan untuk Admin di Web
    public function index()
    {
        $pendaftaran = DB::table('jadwal')
            ->whereNotNull('nama_klien')
            ->orderBy('id_jadwal', 'desc')
            ->get();
        return view('pendaftaran-tes', compact('pendaftaran'));
    }

    // API untuk Submit Pendaftaran dari React Native
    public function storeAPI(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required',
            'nama_lengkap' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
        ]);

        // Menggunakan DB table 'jadwal' agar konsisten
        $update = DB::table('jadwal')->where('id_jadwal', $request->id_jadwal)->update([
            'nama_klien' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'status' => 'Menunggu', 
            'updated_at' => now(),
        ]);

        if ($update) {
            return response()->json(['message' => 'Pendaftaran berhasil dikirim!'], 200);
        }
        return response()->json(['message' => 'Gagal memperbarui jadwal'], 500);
    }

    // API untuk mengambil Riwayat di React Native
    public function getRiwayat() {
        // Mengambil data dari tabel 'jadwal' yang statusnya bukan 'Tersedia'
        // orderBy desc supaya yang terbaru muncul di paling atas
        $riwayat = DB::table('jadwal')
            ->where('status', '!=', 'Tersedia')
            ->whereNotNull('nama_klien') // Memastikan hanya data yang sudah diisi user
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json($riwayat);
    }

    // Update Status dari Dashboard Web Admin
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'komentar' => 'nullable|string'
        ]);

        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'status' => $request->status,
            'komentar' => $request->komentar,
            'updated_at' => now(),
        ]);
        if ($request->status == 'Diterima') {
        // Cek apakah data sudah ada di hasil_tes supaya tidak duplikat
        $exists = DB::table('hasiltes')->where('id_jadwal', $id)->exists();

        if (!$exists) {
            // Ambil data pendaftaran untuk dipindahkan
            $pendaftaran = DB::table('jadwal')->where('id_jadwal', $id)->first();

            DB::table('hasiltes')->insert([
    'id_jadwal'  => $id,
    'nama'       => $pendaftaran->nama_klien, // Sekarang kolom ini sudah ada!
    'no_hp'      => $pendaftaran->no_hp,      // Ini juga sudah ada!
    'status_tes' => 'Proses',
            ]);
        }
    }
        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui');
    }
}