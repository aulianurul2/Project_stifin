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
        Schema::create('klien', function (Blueprint $table) {
    $table->integer('id_klien')->autoIncrement();
    $table->integer('id_user')->nullable();
    $table->string('nama', 100)->nullable();
    $table->string('no_hp', 20)->nullable();
    $table->date('tanggal_lahir')->nullable();
    $table->text('alamat')->nullable();
    $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

    $table->foreign('id_user')->references('id_user')->on('user')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klien');
    }
};
