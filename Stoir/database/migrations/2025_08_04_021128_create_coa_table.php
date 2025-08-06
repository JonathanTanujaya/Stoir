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
        Schema::create('coa', function (Blueprint $table) {
            $table->string('Kode_Akun', 255)->nullable();
            $table->string('Nama_Akun', 255)->nullable();
            $table->string('Saldo_Normal', 255)->nullable();
            $table->string('F4', 255)->nullable();
            $table->string('F5', 255)->nullable();
            $table->string('F6', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa');
    }
};
