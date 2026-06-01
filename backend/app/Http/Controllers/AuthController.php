<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Klien;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    // Menampilkan halaman lupa password
public function showForgotPasswordForm()
{
    return view('auth.forgot-password');
}

// Memproses perubahan password berdasarkan username
public function updatePassword(Request $request)
{
    // 1. Alur untuk Aplikasi Mobile Frontend (Menggunakan EMAIL)
    if ($request->has('email') || $request->expectsJson()) {
        
        // Validasi input JSON dari Axios mobile
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Jalankan JOIN ke tabel klien untuk mencari id_user berdasarkan email
        $user = DB::table('user')
            ->join('klien', 'user.id_user', '=', 'klien.id_user')
            ->where('klien.email', $request->email)
            ->select('user.id_user')
            ->first();

        // Jika email di tabel klien tidak ditemukan
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak terdaftar dalam sistem.'
            ], 404);
        }

        // Update password baru di tabel user menggunakan id_user yang sah
        DB::table('user')
            ->where('id_user', $user->id_user)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Password Anda berhasil diperbarui.'
        ], 200);

    } 
    
    // 2. Alur untuk Web Admin (Menggunakan USERNAME)
    else {
        
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cari langsung ke tabel user berdasarkan username
        $user = DB::table('user')->where('username', $request->username)->first();

        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Username tidak terdaftar dalam sistem.']);
        }

        // Update password langsung berdasarkan username
        DB::table('user')
            ->where('username', $request->username)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return redirect()->route('login')->with('success_reset', 'Password Anda berhasil diperbarui. Silakan masuk!');
    }
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
            'no_hp' => 'required|numeric', 
        ]);

        try {
            // 1. Simpan ke tabel 'user'
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'klien', 
            ]);

            // 2. Simpan detail profil ke tabel 'klien'
            Klien::create([
                'id_user' => $user->id_user, 
                'nama' => $request->nama,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_hp' => '+62' . $request->no_hp, 
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

    /**
     * API Pendaftaran untuk aplikasi mobile (register.tsx)
     */
 public function registerAPI(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'username' => 'required|unique:user,username|max:50',
        'password' => 'required|min:6',
        'tanggal_lahir' => 'required|date',
        'jenis_kelamin' => 'required|in:L,P',
        'golongan_darah' => 'required',
        'no_hp' => 'required',
        'alamat' => 'required|string',
        'institusi' => 'required',
        'sosmed' => 'required',
        'email' => 'required|email',
        'domisili' => 'required',
    ]);

    try {
        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'klien',
        ]);

        Klien::create([
            'id_user' => $user->id_user,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'golongan_darah' => $request->golongan_darah,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'institusi' => $request->institusi,
            'sosmed' => $request->sosmed,
            'email' => $request->email,
            'domisili' => $request->domisili,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pendaftaran berhasil!'], 201);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
    public function loginAPI(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // 1. Cek apakah username dan password cocok
    if (Auth::attempt($credentials)) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. BUAT TOKEN SANCTUM (PENTING!)
        // Token ini yang akan digunakan oleh React Native untuk akses rute yang diproteksi
        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        // 3. Ambil data klien terkait untuk auto-fill yang lebih lengkap
        // Karena data detail seperti domisili/golongan darah ada di tabel 'klien'
        $klien = \App\Models\Klien::where('id_user', $user->id_user)->first();

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil!',
            'token' => $token, // Kirim token ke frontend
            'user' => [
                'nama' => $user->nama,
                'username' => $user->username,
                'role' => $user->role,
                // Ambil No HP dari tabel klien agar konsisten
                'no_hp' => $klien ? $klien->no_hp : null, 
            ]
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Username atau Password salah!'
    ], 401);
}
}