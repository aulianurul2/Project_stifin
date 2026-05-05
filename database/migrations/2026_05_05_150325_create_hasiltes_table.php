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
    $table->string('tipe_tes', 50)->nullable();
    $table->integer('id_admin')->nullable();
    $table->integer('id_jadwal')->nullable();
    $table->date('tanggal')->nullable();
    $table->text('hasil')->nullable();
    $table->string('file_hasil', 255)->nullable();
    $table->string('sertifikat', 255)->nullable();
    $table->enum('status_tes', ['Proses', 'Selesai'])->default('Proses');
    $table->integer('biaya_tes')->default(0);

    $table->foreign('id_klien')->references('id_klien')->on('klien');
    $table->foreign('id_admin')->references('id_admin')->on('admin');
    $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal');
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
