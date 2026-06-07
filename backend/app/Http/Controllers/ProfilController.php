<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman edit profil
     */
    public function index()
    {
        $user  = Auth::user();
        $admin = Admin::where('id_user', $user->id_user)->first();

        return view('edit-profil', compact('user', 'admin'));
    }

    /**
     * Proses update profil (nama, username, password)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:user,username,' . $user->id_user . ',id_user',
            'wa1'      => 'nullable|string|max:20',
            'wa2'      => 'nullable|string|max:20',
        ];

        $messages = [
            'nama.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan oleh akun lain.',
        ];

        // Jika user mengisi password baru, validasi tambahan
        if ($request->filled('password')) {
            $rules['password_lama']    = 'required';
            $rules['password']         = 'required|min:6|confirmed';
            $messages['password_lama.required'] = 'Password lama wajib diisi untuk mengganti password.';
            $messages['password.min']           = 'Password baru minimal 6 karakter.';
            $messages['password.confirmed']     = 'Konfirmasi password baru tidak cocok.';
        }

        $request->validate($rules, $messages);

        // Jika ingin ganti password, verifikasi password lama
        if ($request->filled('password')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama yang Anda masukkan salah.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Update tabel user
            $updateUser = [
                'nama'     => $request->nama,
                'username' => $request->username,
            ];
            if ($request->filled('password')) {
                $updateUser['password'] = Hash::make($request->password);
            }
            DB::table('user')->where('id_user', $user->id_user)->update($updateUser);

            // Helper konversi: 089xxx → 6289xxx
            $formatWa = function (?string $no): ?string {
                if (!$no) return null;
                $no = preg_replace('/\D/', '', $no); // hapus karakter selain angka
                if (str_starts_with($no, '0')) {
                    $no = '62' . substr($no, 1);
                }
                return $no;
            };

            // Update atau buat data admin (wa1, wa2, nama)
            Admin::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama' => $request->nama,
                    'wa1'  => $formatWa($request->wa1),
                    'wa2'  => $formatWa($request->wa2),
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * API: Kembalikan nomor WA admin untuk aplikasi mobile
     */
    public function getAdminContact()
    {
        // Ambil admin pertama yang ditemukan (atau bisa disesuaikan)
        $admin = Admin::first();

        return response()->json([
            'wa1' => $admin->wa1 ?? '6282127747105',
            'wa2' => $admin->wa2 ?? '6281224595556',
        ]);
    }
}