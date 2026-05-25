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
    Schema::table('jadwal', function (Blueprint $table) {
        // Gunakan parameter 'hasColumn' agar Laravel mengecek otomatis jika kolom sudah dibuat sebelumnya
        if (!Schema::hasColumn('jadwal', 'email')) {
            $table->string('email', 150)->nullable()->after('no_hp');
        }
        if (!Schema::hasColumn('jadwal', 'tanggal_lahir')) {
            $table->date('tanggal_lahir')->nullable()->after('email');
        }
        if (!Schema::hasColumn('jadwal', 'jenis_kelamin')) {
            $table->string('jenis_kelamin', 10)->nullable()->after('tanggal_lahir');
        }
        if (!Schema::hasColumn('jadwal', 'golongan_darah')) {
            $table->string('golongan_darah', 5)->nullable()->after('jenis_kelamin');
        }
        if (!Schema::hasColumn('jadwal', 'domisili')) {
            $table->string('domisili', 100)->nullable()->after('golongan_darah');
        }
        if (!Schema::hasColumn('jadwal', 'institusi')) {
            $table->string('institusi', 150)->nullable()->after('domisili');
        }
        if (!Schema::hasColumn('jadwal', 'sosmed')) {
            $table->string('sosmed', 100)->nullable()->after('institusi');
        }
        if (!Schema::hasColumn('jadwal', 'alamat')) {
            $table->text('alamat')->nullable()->after('sosmed');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            //
        });
    }
};
