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
// Di PendaftaranController.php (Fungsi storeAPI)
public function storeAPI(Request $request)
{
    $request->validate([
        'id_jadwal' => 'required',
        'nama_lengkap' => 'required',
        'no_hp' => 'required',
        'email' => 'required|email',
        'alamat' => 'required',
    ]);

    // Update tabel jadwal (Hapus id_user karena kolomnya tidak ada di database kamu)
    $update = DB::table('jadwal')->where('id_jadwal', $request->id_jadwal)->update([
        'nama_klien' => $request->nama_lengkap,
        'no_hp'      => $request->no_hp,
        'email'      => $request->email,
        'alamat'     => $request->alamat,
        'status'     => 'Menunggu',
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

    // 1. Update status di tabel jadwal
    DB::table('jadwal')->where('id_jadwal', $id)->update([
        'status' => $request->status,
        'komentar' => $request->komentar,
        'updated_at' => now(),
    ]);

    if ($request->status == 'Diterima') {
        $jadwal = DB::table('jadwal')->where('id_jadwal', $id)->first();

        // 2. CEK ATAU BUAT DATA KLIEN
        // Kita cari klien berdasarkan email atau no_hp yang diinput saat daftar
        $klien = DB::table('klien')->where('email', $jadwal->email)->first();
        
        if (!$klien) {
            // Jika klien belum terdaftar di tabel klien, buat baru otomatis
            $id_klien = DB::table('klien')->insertGetId([
                'nama' => $jadwal->nama_klien,
                'no_hp' => $jadwal->no_hp,
                'email' => $jadwal->email,
                'alamat' => $jadwal->alamat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id_klien = $klien->id_klien;
        }

        // 3. INSERT KE HASILTES DENGAN ID_KLIEN YANG SUDAH DIDAPAT
        $exists = DB::table('hasiltes')->where('id_jadwal', $id)->exists();

        if (!$exists) {
            DB::table('hasiltes')->insert([
                'id_jadwal'  => $id,
                'id_klien'   => $id_klien, // Sekarang sudah pasti ada ID-nya
                'status_tes' => 'Proses',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui');
}}