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
        Schema::create('part_penerimaan_bonus_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 4)->nullable();
            $table->string('NoPenerimaanBonus', 15)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('QtySupply')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_penerimaan_bonus_detail');
    }
};
