<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Data User
        DB::table('user')->insert([
            ['id_user' => 1, 'nama' => 'Admin', 'username' => 'Admin', 'password' => Hash::make('kaki1234'), 'role' => 'admin'],
            ['id_user' => 2, 'nama' => 'calvin', 'username' => 'calvin', 'password' => Hash::make('kaki1234'), 'role' => 'klien'],
        ]);

        // Data Admin
        DB::table('admin')->insert([
            ['id_admin' => 1, 'id_user' => 1, 'nama' => 'Admin'],
        ]);

        // Data Klien
        DB::table('klien')->insert([
            ['id_klien' => 4, 'id_user' => 2, 'nama' => 'Calvin William Wijaya', 'no_hp' => '+628983773516', 'tanggal_lahir' => '2005-02-04', 'alamat' => 'Subang', 'jenis_kelamin' => 'L'],
        ]);

        // Data Jadwal
        DB::table('jadwal')->insert([
            ['id_jadwal' => 1, 'id_klien' => 4, 'id_admin' => 1, 'nama_klien' => 'Calvin William Wijaya', 'no_hp' => '+628983773516', 'tanggal' => '2026-04-01', 'waktu' => '13:00:00', 'lokasi' => 'Visit Office', 'status' => 'Diterima', 'komentar' => 'Sabar'],
        ]);

        // Data Hasil Tes
        DB::table('hasiltes')->insert([
            ['id_tes' => 1, 'id_klien' => 4, 'tipe_tes' => 'Tes Kognitif', 'id_admin' => 1, 'id_jadwal' => 1, 'tanggal' => '2026-04-28', 'hasil' => 'Sensing', 'file_hasil' => '1777363790_Jobsheet_PBO2_Calvin.pdf', 'status_tes' => 'Selesai', 'biaya_tes' => 150000],
        ]);
    }
}