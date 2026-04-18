<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KlienController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama (Login)
// Saat membuka http://127.0.0.1:8000, rute ini yang pertama kali dijalankan
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// 2. Rute Registrasi Akun
// Menampilkan form daftar
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Memproses data yang dikirim dari form daftar
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// 3. Rute yang Membutuhkan Login (Middleware Auth)
// Semua rute di dalam grup ini hanya bisa diakses jika user sudah login
Route::middleware(['auth'])->group(function () {
   Route::get('/dashboard', function () {
    // Ambil data statistik
    $countKlien = DB::table('klien')->count();
    $countJadwal = DB::table('jadwal')->count(); // <--- Pastikan variabel ini ada
    $countHasil = DB::table('hasiltes')->count();

    // Data Terbaru
    $jadwalTerbaru = DB::table('jadwal')
        ->select('nama_klien', 'tanggal', 'waktu', 'status')
        ->latest('id_jadwal')
        ->limit(5)
        ->get();

    $hasilTerbaru = DB::table('hasiltes')
        ->join('klien', 'hasiltes.id_klien', '=', 'klien.id_klien')
        ->select('klien.nama', 'hasiltes.hasil', 'hasiltes.tanggal')
        ->latest('id_tes')
        ->limit(5)
        ->get();

    // Bagian yang sangat penting: pastikan 'countJadwal' tertulis di sini
    return view('dashboard', compact(
        'countKlien', 
        'countJadwal', 
        'countHasil', 
        'jadwalTerbaru', 
        'hasilTerbaru'
    ));
})->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/kelola-klien', [KlienController::class, 'index'])->name('kelola-klien');
// Route untuk Update
Route::put('/kelola-klien/{id}', [KlienController::class, 'update'])->name('klien.update');
// Route untuk Delete
Route::delete('/kelola-klien/{id}', [KlienController::class, 'destroy'])->name('klien.destroy');
