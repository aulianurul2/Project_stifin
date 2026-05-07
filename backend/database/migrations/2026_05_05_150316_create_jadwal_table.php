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
    $table->string('nama_klien', 100)->nullable();
    $table->string('no_hp', 15)->nullable();
    $table->date('tanggal')->nullable();
    $table->time('waktu')->nullable();
    $table->text('lokasi')->nullable();
    $table->string('status', 50)->nullable();
    $table->text('komentar')->nullable();

    $table->foreign('id_klien')->references('id_klien')->on('klien');
    $table->foreign('id_admin')->references('id_admin')->on('admin');
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
