<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom nama_kota ke tabel pendaftaran (jadwal) dan hasiltes
     * agar harga per kota Ciayumajakuning bisa disimpan dan ditampilkan.
     */
    public function up(): void
    {
        // Tambahkan nama_kota ke tabel jadwal (dipakai saat pendaftaran)
        Schema::table('jadwal', function (Blueprint $table) {
            // nama_kota: diisi jika is_luar_subang = true, misal "Kota Cirebon"
            if (!Schema::hasColumn('jadwal', 'nama_kota')) {
                $table->string('nama_kota')->nullable()->after('bukti_transfer');
            }
            // biaya: nominal biaya sesuai kota/wilayah
            if (!Schema::hasColumn('jadwal', 'biaya')) {
                $table->unsignedInteger('biaya')->default(550000)->after('nama_kota');
            }
        });

        // Tambahkan nama_kota ke tabel hasiltes agar HasilTesController bisa baca
        Schema::table('hasiltes', function (Blueprint $table) {
            if (!Schema::hasColumn('hasiltes', 'nama_kota')) {
                $table->string('nama_kota')->nullable()->after('biaya_tes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn(['nama_kota', 'biaya']);
        });

        Schema::table('hasiltes', function (Blueprint $table) {
            $table->dropColumn('nama_kota');
        });
    }
};