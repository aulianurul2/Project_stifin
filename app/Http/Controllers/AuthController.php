<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Klien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
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

        // 1. Coba Autentikasi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // 2. Ambil data user yang sedang login
            $user = Auth::user();

            // 3. Cek Role: Jika bukan admin, gagalkan login (karena ini login khusus admin)
            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'login' => 'Akses ditolak! Akun Anda tidak memiliki hak akses Admin.',
                ])->withInput($request->only('username'));
            }

            // 4. Jika admin, arahkan ke dashboard
            return redirect()->intended('dashboard');
        }

        // Jika gagal autentikasi
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
     * Proses pendaftaran klien baru
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
            'no_hp' => 'required|numeric', // Validasi input nomor hp
        ]);

        try {
            // 1. Simpan ke tabel 'user'
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'klien', // Otomatis menjadi klien
            ]);

            // 2. Simpan detail profil ke tabel 'klien'
            Klien::create([
                'id_user' => $user->id_user,
                'nama' => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_hp' => '+62' . $request->no_hp, // Gabungkan prefix +62
            ]);

            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login.');

        } catch (\Exception $e) {
            Log::error("Pendaftaran Gagal: " . $e->getMessage());
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