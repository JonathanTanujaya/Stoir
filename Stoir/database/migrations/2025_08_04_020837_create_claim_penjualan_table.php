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
        Schema::create('claim_penjualan', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoClaim', 15)->nullable();
            $table->date('TglClaim')->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->string('Keterangan', 200)->nullable();
            $table->string('Status', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_penjualan');
    }
};
