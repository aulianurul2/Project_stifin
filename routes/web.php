<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\HasilTesController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- GUEST ROUTES (Tanpa Login) ---

// Halaman utama (/) mau diarahkan ke login juga boleh
Route::get('/', function() {
    return redirect()->route('login');
});

// Route GET untuk nampilin form login (URL: /login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Route POST untuk proses kirim data login (URL: /login)
Route::post('/login', [AuthController::class, 'login']);

// Register tetap sama
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.process');

// --- AUTH ROUTES (Hanya Setelah Login) ---
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard (Langsung panggil Controller, jangan pakai Closure/function lagi)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Kelola Klien
    Route::prefix('kelola-klien')->group(function () {
        Route::get('/', [KlienController::class, 'index'])->name('kelola-klien');
        Route::put('/{id}', [KlienController::class, 'update'])->name('klien.update');
        Route::delete('/{id}', [KlienController::class, 'destroy'])->name('klien.destroy');
    });

    // 3. Pendaftaran Tes
    Route::prefix('pendaftaran-tes')->group(function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran-tes');
        Route::put('/{id}', [PendaftaranController::class, 'updateStatus'])->name('pendaftaran.update');
    });

    // 4. Jadwal Tes
    Route::prefix('jadwal-tes')->group(function () {
        Route::get('/', [JadwalController::class, 'index'])->name('jadwal-tes');
        Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    });

    // 5. Hasil Tes
    Route::prefix('hasil-tes')->group(function () {
        Route::get('/', [HasilTesController::class, 'index'])->name('hasil-tes');
        Route::put('/{id}', [HasilTesController::class, 'update'])->name('hasil.update');
    });

    // 6. Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});
