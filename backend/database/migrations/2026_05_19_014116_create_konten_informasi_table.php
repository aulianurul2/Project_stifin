<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konten_informasi', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('description');
            $table->string('icon', 50)->default('information-circle-outline');
            $table->string('color', 10)->default('#eff6ff'); 
            $table->string('text_color', 10)->default('#1e40af'); 
            $table->string('image')->nullable(); // <-- TAMBAHKAN KOLOM INI
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konten_informasi');
    }
};