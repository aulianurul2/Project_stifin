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
        $table->string('nik', 20)->nullable()->after('no_hp');
        $table->string('email', 100)->nullable()->after('nik');
        $table->text('alamat')->nullable()->after('email');
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
