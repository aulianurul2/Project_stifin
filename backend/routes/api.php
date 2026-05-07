<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PendaftaranController;

// Route untuk pendaftaran dari React Native
Route::post('/addnew', [AuthController::class, 'registerAPI']);

// Route untuk login (nanti)
Route::post('/login', [AuthController::class, 'loginAPI']);

// routes/api.php
Route::get('/jadwal-tersedia', [JadwalController::class, 'getJadwalApi']);

// Route untuk submit pendaftaran dari React Native
Route::post('/pendaftaran/submit', [PendaftaranController::class, 'storeAPI']);

Route::get('/riwayat-pendaftaran', [PendaftaranController::class, 'getRiwayat']);