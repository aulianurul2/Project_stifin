<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->integer('id_jadwal')->autoIncrement();
            $table->integer('id_klien')->nullable();
            $table->integer('id_admin')->nullable();
            
            // Kolom data pendaftar (nullable karena Admin buat slot kosong dulu)
            $table->string('nama_klien', 100)->nullable();
            $table->string('no_hp', 15)->nullable();
            
            // Kolom inti Jadwal
            $table->date('tanggal')->nullable();
            $table->time('waktu')->nullable();
            
            // Lokasi diset string dengan default agar mudah dibaca di mobile
            $table->string('lokasi', 100)->default('Kantor Cabang'); 
            
            // Status dan Kapasitas
            $table->string('status', 50)->default('Tersedia'); 
            $table->integer('kuota')->default(1); 
            
            $table->text('komentar')->nullable();
            $table->timestamps(); 

            // Foreign Keys
            $table->foreign('id_klien')->references('id_klien')->on('klien')->onDelete('set null');
            $table->foreign('id_admin')->references('id_admin')->on('admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};