<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\KontenInformasiController;
use App\Http\Controllers\PanduanController;

// Rute Publik (Tanpa Login)
Route::post('/addnew', [AuthController::class, 'registerAPI']);
Route::post('/login', [AuthController::class, 'loginAPI']);
Route::post('/forgot-password', [AuthController::class, 'updatePassword']);
Route::get('/jadwal-tersedia', [JadwalController::class, 'getJadwalApi']);
Route::get('/informasi-tes', [KontenInformasiController::class, 'getApiInformasi']);
Route::apiResource('panduan', PanduanController::class);

// Rute Privat (Harus Login/Membawa Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Ambil data profil LENGKAP (User + Klien) untuk auto-fill
    Route::get('/profile', function (Request $request) {
        $user = $request->user();
        
        // Cari data detail di tabel klien berdasarkan id_user
        $klien = \App\Models\Klien::where('id_user', $user->id_user)->first();

        // Gabungkan data user dan klien menjadi satu response JSON
        return response()->json([
            'id_user'        => $user->id_user,
            'nama'           => $user->nama,
            'username'       => $user->username,
            // Data dari tabel klien
            'no_hp'          => $klien->no_hp ?? '',
            'tanggal_lahir'  => $klien->tanggal_lahir ?? '',
            'jenis_kelamin'  => $klien->jenis_kelamin ?? 'L',
            'golongan_darah' => $klien->golongan_darah ?? '-',
            'email'          => $klien->email ?? '',
            'alamat'         => $klien->alamat ?? '',
            'institusi'      => $klien->institusi ?? '',
            'sosmed'         => $klien->sosmed ?? '',
            'domisili'       => $klien->domisili ?? '',
        ]);
    });

    Route::put('/profile/update', [\App\Http\Controllers\KlienController::class, 'updateProfile']);
    Route::post('/pendaftaran/submit', [PendaftaranController::class, 'storeAPI']);
    Route::get('/riwayat-pendaftaran', [PendaftaranController::class, 'getRiwayat']);
    Route::get('/hasil-tes-saya', [PendaftaranController::class, 'hasilTesSaya']);
    Route::put('/pendaftaran/{id}/batalkan', [PendaftaranController::class, 'batalkanPendaftaranApi']);
    Route::put('/pendaftaran/{id}/reschedule', [PendaftaranController::class, 'reschedulePendaftaranApi']);
    
});