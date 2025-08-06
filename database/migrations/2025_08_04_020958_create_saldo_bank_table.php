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
        Schema::create('saldo_bank', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoRekening', 50)->nullable();
            $table->date('TglProses')->nullable();
            $table->string('Keterangan', 500)->nullable();
            $table->decimal('Debet', 19, 4)->nullable();
            $table->decimal('Kredit', 19, 4)->nullable();
            $table->decimal('Saldo', 19, 4)->nullable();
            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_bank');
    }
};
