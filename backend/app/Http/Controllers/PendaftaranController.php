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
            ->paginate(10);
        return view('pendaftaran-tes', compact('pendaftaran'));
    }

    // API untuk Submit Pendaftaran dari React Native
   public function storeAPI(Request $request)
{
    // 1. Tambahkan field baru ke dalam validasi (gunakan 'nullable' agar tidak crash jika kosong)
    $request->validate([
        'id_jadwal'      => 'required',
        'nama_klien'     => 'required',
        'no_hp'          => 'required',
        'email'          => 'required|email',
        'tanggal_lahir'  => 'nullable|date',
        'jenis_kelamin'  => 'nullable|string',
        'golongan_darah' => 'nullable|string',
        'domisili'       => 'nullable|string',
        'institusi'      => 'nullable|string',
        'sosmed'         => 'nullable|string',
        'alamat'         => 'required',
    ]);

    $user = $request->user();

    $klien = DB::table('klien')
        ->where('id_user', $user->id_user)
        ->first();

    if (!$klien) {
        return response()->json([
            'message' => 'Data klien tidak ditemukan'
        ], 404);
    }

    // Ambil kuota jadwal saat ini untuk memastikan slot masih ada
    $jadwal = DB::table('jadwal')->where('id_jadwal', $request->id_jadwal)->first();
    if (!$jadwal || $jadwal->kuota <= 0) {
        return response()->json([
            'message' => 'Slot kuota untuk jadwal ini sudah penuh'
        ], 400);
    }

    // 2. Masukkan semua variabel baru ke dalam proses update database
    $update = DB::table('jadwal')
        ->where('id_jadwal', $request->id_jadwal)
        ->update([
            'id_klien'       => $klien->id_klien, 
            'nama_klien'     => $request->nama_klien,
            'no_hp'          => $request->no_hp,
            'email'          => $request->email,
            'tanggal_lahir'  => $request->tanggal_lahir,  // <-- Ditambahkan
            'jenis_kelamin'  => $request->jenis_kelamin,  // <-- Ditambahkan
            'golongan_darah' => $request->golongan_darah, // <-- Ditambahkan
            'domisili'       => $request->domisili,       // <-- Ditambahkan
            'institusi'      => $request->institusi,      // <-- Ditambahkan
            'sosmed'         => $request->sosmed,         // <-- Ditambahkan
            'alamat'         => $request->alamat,
            'status'         => 'Menunggu',
            'kuota'          => $jadwal->kuota - 1, 
            'updated_at'     => now(),
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
    $klien = DB::table('klien')->where('id_user', $user->id_user)->first();

    if (!$klien) return response()->json([]);

    // Query ini sekarang akan menampilkan semua riwayat karena id_klien tidak dihapus
    $riwayat = DB::table('jadwal')
        ->leftJoin('hasiltes', 'jadwal.id_jadwal', '=', 'hasiltes.id_jadwal')
        ->select('jadwal.*', 'hasiltes.status_tes', 'hasiltes.file_hasil', 'hasiltes.file_detail')
        ->where('jadwal.id_klien', $klien->id_klien) // Kunci agar user melihat riwayatnya sendiri
        ->orderBy('jadwal.updated_at', 'desc')
        ->get();

    return response()->json($riwayat);
    }

    public function hasilTesSaya(Request $request)
    {
        $user = $request->user();

        $klien = DB::table('klien')
            ->where('id_user', $user->id_user)
            ->first();

        if (!$klien) {
            return response()->json([]);
        }

        $data = DB::table('hasiltes')
            ->join('jadwal', 'hasiltes.id_jadwal', '=', 'jadwal.id_jadwal')
            ->where('hasiltes.id_klien', $klien->id_klien)
            ->select(
                'hasiltes.*',
                'jadwal.id_jadwal',
                'jadwal.tanggal',
                'jadwal.waktu',
                'jadwal.status',
                'jadwal.komentar'
            )
            ->orderBy('hasiltes.id_tes', 'desc')
            ->get();

        return response()->json($data);
    }

    // Update Status dari Dashboard Web Admin
public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required',
        'komentar' => 'nullable|string'
    ]);

    // AMAN: Ubah input menjadi huruf kecil semua dan hapus spasi tak terlihat
    $statusInput = strtolower(trim($request->status));

    $jadwalLama = DB::table('jadwal')->where('id_jadwal', $id)->first();

    if (!$jadwalLama) {
        return response()->json(['message' => 'Data jadwal tidak ditemukan'], 404);
    }

    // 1. JIKA ADMIN MENOLAK PENDAFTARAN
    if ($statusInput == 'ditolak') {
        $kuotaBaru = ($jadwalLama->status == 'Ditolak') ? $jadwalLama->kuota : $jadwalLama->kuota + 1;

        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'status' => 'Ditolak',
            'komentar' => $request->komentar,
            'kuota' => $kuotaBaru,
            'updated_at' => now(),
        ]);

        DB::table('hasiltes')->where('id_jadwal', $id)->delete();

    // 2. JIKA ADMIN MEMILIH "BUKA KEMBALI" (Status dikirim: 'Tersedia' atau 'tersedia')
    } else if ($statusInput == 'tersedia') {


        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'id_klien'       => null,
            'nama_klien'     => null,
            'no_hp'          => null,
            'email'          => null,
            'alamat'         => null,
            'tanggal_lahir'  => null, 
            'jenis_kelamin'  => null,
            'golongan_darah' => null,
            'domisili'       => null,
            'institusi'      => null,
            'sosmed'         => null,
            'status'         => 'Tersedia', // Simpan string asli ke DB
            'komentar'       => null,
            'kuota'          => 1,
            'updated_at'     => now()
        ]);


        DB::table('hasiltes')->where('id_jadwal', $id)->delete();

    // 3. UNTUK STATUS LAINNYA
    } else {
        DB::table('jadwal')->where('id_jadwal', $id)->update([
            'status' => $request->status,
            'komentar' => $request->komentar,
            'updated_at' => now(),
        ]);
    }

    // 4. JIKA STATUS DITERIMA
    if ($request->status == 'Diterima') {
        $klien = DB::table('klien')->where('id_klien', $jadwalLama->id_klien)->first();
        
        if (!$klien) {
            $id_klien = DB::table('klien')->insertGetId([
                'nama' => $jadwalLama->nama_klien,
                'no_hp' => $jadwalLama->no_hp,
                'email' => $jadwalLama->email,
                'alamat' => $jadwalLama->alamat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id_klien = $klien->id_klien;
        }

        $exists = DB::table('hasiltes')->where('id_jadwal', $id)->exists();

        if (!$exists) {
            DB::table('hasiltes')->insert([
                'id_jadwal'  => $id,
                'id_klien'   => $id_klien, 
                'status_tes' => 'Proses',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('hasiltes')->where('id_jadwal', $id)->update([
                'status_tes' => 'Proses',
                'updated_at' => now()
            ]);
        }
    }

    // WAJIB: Gunakan json response agar AJAX Anda membaca blok `success` dengan benar
    return response()->json([
        'success' => true,
        'message' => 'Status berhasil diperbarui dan data telah dibersihkan.'
    ], 200);
}
    // =======================================================
    // PERBAIKAN LOGIKA: API PEMBATALAN JADWAL DARI SISI KLIEN
    // =======================================================
    public function batalkanPendaftaranApi(Request $request, $id)
    {
        $jadwal = DB::table('jadwal')->where('id_jadwal', $id)->first();

        if (!$jadwal) {
            return response()->json(['message' => 'Data pendaftaran tidak ditemukan'], 404);
        }

        // Hapus data klien di baris jadwal, kembalikan status menjadi 'Tersedia', dan tambahkan kuota kembali (+1)
        $update = DB::table('jadwal')
            ->where('id_jadwal', $id)
            ->update([
                'id_klien'   => null,
                'nama_klien' => null,
                'no_hp'      => null,
                'email'      => null,
                'tanggal_lahir' => null,
                'jenis_kelamin' => null,
                'golongan_darah' => null,
                'domisili' => null,
                'institusi' => null,
                'sosmed' => null,
                'alamat'     => null,
                'status'     => 'Tersedia',
                'komentar'   => null,
                'kuota'      => $jadwal->kuota + 1,
                'updated_at' => now()
            ]);

        if ($update) {
            // Bersihkan data hasiltes jika pendaftaran ini sebelumnya sudah disetujui admin
            DB::table('hasiltes')->where('id_jadwal', $id)->delete();

            return response()->json(['message' => 'Pendaftaran berhasil dibatalkan, slot kembali tersedia'], 200);
        }
        
        return response()->json(['message' => 'Gagal mematalkan pendaftaran'], 500);
    }

    // API Pengajuan Reschedule (Pindah ke slot jadwal kosong lain)
    public function reschedulePendaftaranApi(Request $request, $id)
    {
        $request->validate([
            'id_jadwal_baru' => 'required'
        ]);

        $id_jadwal_lama = $id;
        $id_jadwal_baru = $request->id_jadwal_baru;

        $pendaftaranLama = DB::table('jadwal')->where('id_jadwal', $id_jadwal_lama)->first();
        $jadwalBaru = DB::table('jadwal')->where('id_jadwal', $id_jadwal_baru)->first();

        if (!$pendaftaranLama) {
            return response()->json(['message' => 'Data pendaftaran tidak ditemukan'], 404);
        }

        if (!$jadwalBaru || $jadwalBaru->kuota <= 0) {
            return response()->json(['message' => 'Jadwal baru pilihan Anda sudah penuh'], 400);
        }

        // 1. Salin data pendaftaran lama ke record slot jadwal pilihan baru & kurangi kuota barunya
        $reschedule = DB::table('jadwal')
            ->where('id_jadwal', $id_jadwal_baru)
            ->update([
                'id_klien'   => $pendaftaranLama->id_klien,
                'nama_klien' => $pendaftaranLama->nama_klien,
                'no_hp'      => $pendaftaranLama->no_hp,
                'email'      => $pendaftaranLama->email,
                'tanggal_lahir' => $pendaftaranLama->tanggal_lahir,
                'jenis_kelamin' => $pendaftaranLama->jenis_kelamin,
                'golongan_darah' => $pendaftaranLama->golongan_darah,
                'domisili' => $pendaftaranLama->domisili,
                'institusi' => $pendaftaranLama->institusi,
                'sosmed' => $pendaftaranLama->sosmed,
                'alamat'     => $pendaftaranLama->alamat,
                'status'     => 'Menunggu', 
                'kuota'      => $jadwalBaru->kuota - 1,
                'komentar'   => 'Pengajuan Reschedule dari jadwal lama tanggal: ' . $pendaftaranLama->tanggal,
                'updated_at' => now()
            ]);

        if ($reschedule) {
            // 2. Kosongkan kembali slot jadwal lama & kembalikan kuotanya (+1)
            DB::table('jadwal')->where('id_jadwal', $id_jadwal_lama)->update([
                'id_klien'   => null,
                'nama_klien' => null,
                'no_hp'      => null,
                'email'      => null,
                'alamat'     => null,
                'tanggal_lahir' => null,
                'jenis_kelamin' => null,
                'golongan_darah' => null,
                'domisili' => null,
                'institusi' => null,
                'sosmed' => null,
                'status'     => 'Tersedia',
                'komentar'   => null,
                'kuota'      => $pendaftaranLama->kuota + 1,
                'updated_at' => now()
            ]);

            // 3. Alihkan relasi data hasiltes (jika ada)
            DB::table('hasiltes')->where('id_jadwal', $id_jadwal_lama)->update([
                'id_jadwal'  => $id_jadwal_baru,
                'status_tes' => 'Proses',
                'updated_at' => now()
            ]);

            return response()->json(['message' => 'Reschedule berhasil diajukan'], 200);
        }

        return response()->json(['message' => 'Gagal memproses reschedule'], 500);
    }
}