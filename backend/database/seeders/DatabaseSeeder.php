<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Matikan pengecekan foreign key agar bisa truncate tabel dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Bersihkan data lama agar tidak duplikat/error saat running ulang
        DB::table('admin')->truncate();
        DB::table('user')->truncate();

        // 1. Masukkan data ke tabel 'user' untuk akun login
        // Menghapus 'created_at' karena kolom tidak ditemukan di database Anda
        DB::table('user')->insert([
            'id_user' => 1,
            'nama' => 'Administrator STIFIn',
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Password: admin123
            'role' => 'admin',
        ]);

        // 2. Masukkan data ke tabel 'admin' (relasi ke user id 1)
        DB::table('admin')->insert([
            'id_admin' => 1,
            'id_user' => 1,
            'nama' => 'Admin Utama',
        ]);

        // Hidupkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}