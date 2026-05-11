<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klien', function (Blueprint $table) {
            $table->integer('id_klien')->autoIncrement();
            $table->integer('id_user')->nullable();
            $table->string('nama', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('golongan_darah', 5);
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->string('institusi', 100);
            $table->string('sosmed', 100); // FB/Instagram
            $table->string('email', 100);
            $table->string('domisili', 100);
            
            $table->foreign('id_user')->references('id_user')->on('user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klien');
    }
};