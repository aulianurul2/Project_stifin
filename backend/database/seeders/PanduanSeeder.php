<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Panduan;

class PanduanSeeder extends Seeder
{
    public function run(): void
    {
        Panduan::create([
            'title' => 'Alur Pendaftaran Tes STIFIn',
            'content' => 'Panduan lengkap mengenai tata cara pendaftaran, pemilihan lokasi, konfirmasi datapersonal, hingga proses unduh sertifikat resmi hasil analisis kecerdasan.',
            'category' => 'Pendaftaran',
            'icon' => 'book-outline'
        ]);
    }
}