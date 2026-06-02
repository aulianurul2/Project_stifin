<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KlienController extends Controller
{
    public function index()
    {
        $klien = DB::table('klien')
            ->leftJoin('jadwal', 'klien.id_klien', '=', 'jadwal.id_klien')
            ->select('klien.*', 'jadwal.status as status_jadwal')
            ->get();

        return view('kelola-klien', compact('klien'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi semua field yang dikirim dari form admin lengkap
        $request->validate([
            'nama'           => 'required|string|max:100',
            'no_hp'          => 'required|string|max:15',
            'email'          => 'nullable|email|max:100',
            'tanggal_lahir'  => 'nullable|date',
            'jenis_kelamin'  => 'nullable|string|in:L,P',
            'golongan_darah' => 'nullable|string|max:3',
            'institusi'      => 'nullable|string|max:150',
            'sosmed'         => 'nullable|string|max:100',
            'domisili'       => 'nullable|string|max:100',
            'alamat'         => 'nullable|string'
        ]);

        // 2. Ambil data klien untuk mendapatkan id_user (agar nama di tabel user ikut sinkron)
        $klien = DB::table('klien')->where('id_klien', $id)->first();

        if ($klien) {
            // Update nama di tabel akun utama (user) jika diperlukan agar tetap sinkron
            DB::table('user')->where('id_user', $klien->id_user)->update([
                'nama' => $request->nama,
            ]);
        }

        // 3. Update seluruh data profile pendaftaran lengkap di tabel klien
        DB::table('klien')->where('id_klien', $id)->update([
            'nama'           => $request->nama,
            'no_hp'          => $request->no_hp,
            'email'          => $request->email,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'golongan_darah' => $request->golongan_darah,
            'institusi'      => $request->institusi,
            'sosmed'         => $request->sosmed,
            'domisili'       => $request->domisili,
            'alamat'         => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Data klien berhasil diperbarui secara lengkap');
    }

    public function destroy($id)
    {
        // Ambil data klien
        $klien = DB::table('klien')
            ->where('id_klien', $id)
            ->first();

        if (!$klien) {
            return redirect()->back()->with('error', 'Klien tidak ditemukan');
        }

        // Hapus user -> klien ikut kehapus otomatis
        DB::table('user')
            ->where('id_user', $klien->id_user)
            ->delete();

        return redirect()->back()->with('success', 'Klien berhasil dihapus');
    }

    public function updateProfile(Request $request)
    {
        $userId = auth()->id(); // Ambil ID User dari token login Sanctum

        $klien = DB::table('klien')
            ->where('id_user', $userId)
            ->first();

        // Validasi input, email harus unik di tabel 'user' kecuali milik dia sendiri
        $request->validate([
            'nama'   => 'required|string|max:100',
            'no_hp'  => 'required|string|max:15',
            'alamat' => 'required|string',
            'email'  => [
                'required',
                'email',
                'max:100',
                Rule::unique('klien', 'email')
                    ->ignore($klien->id_klien, 'id_klien')
            ],
        ]);

        // 1. Update data akun login utama (tabel user)
        DB::table('user')->where('id_user', $userId)->update([
            'nama' => $request->nama,
        ]);

        // 2. Update data detail profil (tabel klien) dari mobile API
        DB::table('klien')->where('id_user', $userId)->update([
            'nama'           => $request->nama,
            'no_hp'          => $request->no_hp,
            'email'          => $request->email, 
            'tanggal_lahir'  => $request->tanggal_lahir,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'golongan_darah' => $request->golongan_darah,
            'alamat'         => $request->alamat,
            'institusi'      => $request->institusi,
            'sosmed'         => $request->sosmed,
            'domisili'       => $request->domisili,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil dan data akun berhasil diperbarui!'
        ], 200);
    }
}