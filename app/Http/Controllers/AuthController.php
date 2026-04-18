<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Klien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login (paling pertama saat serve)
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Menggunakan Auth::attempt untuk memvalidasi username dan password terenkripsi
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            // Mengarahkan ke dashboard setelah login berhasil
            return redirect()->intended('dashboard');
        }

        // Jika gagal, kembali dengan pesan error
        return back()->withErrors([
            'login' => 'Username atau Password salah!',
        ])->withInput($request->only('username'));
    }

    /**
     * Menampilkan halaman pendaftaran akun
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses penyimpanan data user baru ke database
     */
   public function register(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'username' => 'required|unique:user,username|max:50',
        'password' => 'required|min:6',
        'tanggal_lahir' => 'required|date',
        'alamat' => 'required|string',
        'jenis_kelamin' => 'required|in:L,P',
    ]);

    try {
        // 2. Simpan ke tabel 'user' dahulu
        $user = \App\Models\User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'klien', // Otomatis klien sesuai permintaan sebelumnya
        ]);

        // 3. Simpan detail profil ke tabel 'klien'
        \App\Models\Klien::create([
            'id_user' => $user->id_user, // ID dari user yang baru saja dibuat
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silahkan login.');

    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}