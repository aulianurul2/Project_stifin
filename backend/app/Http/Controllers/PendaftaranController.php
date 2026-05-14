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

    $user = $request->user();

    // Cari klien berdasarkan akun login
    $klien = DB::table('klien')
        ->where('id_user', $user->id_user)
        ->first();

    if (!$klien) {
        return response()->json([
            'message' => 'Data klien tidak ditemukan'
        ], 404);
    }

    $update = DB::table('jadwal')
        ->where('id_jadwal', $request->id_jadwal)
        ->update([
            'id_klien'   => $klien->id_klien, // INI PENTING
            'nama_klien' => $request->nama_lengkap,
            'no_hp'      => $request->no_hp,
            'email'      => $request->email,
            'alamat'     => $request->alamat,
            'status'     => 'Menunggu',
            'updated_at' => now(),
        ]);

    if ($update) {
        return response()->json([
            'message' => 'Pendaftaran berhasil'
        ], 200);
    }

    return response()->json([
        'message' => 'Gagal daftar'
    ], 500);
}
    // API untuk mengambil Riwayat di React Native
    public function getRiwayat(Request $request)
{
    $user = $request->user();

    $klien = DB::table('klien')
        ->where('id_user', $user->id_user)
        ->first();

    if (!$klien) {
        return response()->json([]);
    }

    $riwayat = DB::table('jadwal')
        ->where('id_klien', $klien->id_klien)
        ->where('status', '!=', 'Tersedia')
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
        $klien = DB::table('klien')
    ->where('id_klien', $jadwal->id_klien)
    ->first();
        
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