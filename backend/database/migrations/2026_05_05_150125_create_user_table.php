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
       Schema::create('user', function (Blueprint $table) {
    $table->integer('id_user')->autoIncrement();
    $table->string('nama', 100)->nullable();
    $table->string('username', 50)->nullable();
    $table->string('password', 255)->nullable();
    $table->string('role', 20)->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
