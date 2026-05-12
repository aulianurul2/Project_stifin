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
        Schema::create('hasiltes', function (Blueprint $table) {
            $table->integer('id_tes')->autoIncrement();
            $table->integer('id_klien')->nullable();
            $table->integer('id_admin')->nullable();
            $table->integer('id_jadwal')->nullable();
            
            $table->date('tanggal')->nullable();
            
            // Kolom untuk 2 file upload
            $table->string('file_hasil', 255)->nullable(); // Ini untuk Sertifikat/Ringkasan
            $table->string('file_detail', 255)->nullable(); // Ini untuk Dokumen Hasil Lengkap
            
            $table->enum('status_tes', ['Proses', 'Selesai'])->default('Proses');
            $table->integer('biaya_tes')->default(0);
            
            $table->timestamps(); // Bagus untuk tracking waktu input

            // Foreign Key Constraints
            $table->foreign('id_klien')->references('id_klien')->on('klien')->onDelete('cascade');
            $table->foreign('id_admin')->references('id_admin')->on('admin')->onDelete('set null');
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasiltes');
    }
};