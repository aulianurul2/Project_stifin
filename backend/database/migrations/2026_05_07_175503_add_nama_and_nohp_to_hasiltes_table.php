<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasiltes', function (Blueprint $table) {
            // Menambahkan kolom nama dan no_hp setelah kolom id_klien
            $table->string('nama')->nullable()->after('id_klien');
            $table->string('no_hp')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('hasiltes', function (Blueprint $table) {
            // Menghapus kembali kolom jika migration di-rollback
            $table->dropColumn(['nama', 'no_hp']);
        });
    }
};