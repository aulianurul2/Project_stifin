<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id('id_pemasukan');
            $table->unsignedInteger('id_jadwal');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};