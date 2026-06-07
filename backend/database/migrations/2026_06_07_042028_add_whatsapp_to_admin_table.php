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
    Schema::table('admin', function (Blueprint $table) {
        $table->string('wa1', 20)->nullable()->after('nama')->comment('Nomor WhatsApp Admin 1');
        $table->string('wa2', 20)->nullable()->after('wa1')->comment('Nomor WhatsApp Admin 2');
    });

    // Isi nilai default untuk baris admin yang sudah ada
    DB::table('admin')->update([
        'wa1' => '6282127747105',
        'wa2' => '6281224595556',
    ]);
}

public function down(): void
{
    Schema::table('admin', function (Blueprint $table) {
        $table->dropColumn(['wa1', 'wa2']);
    });
}
};
