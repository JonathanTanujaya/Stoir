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
        Schema::create('part_penerimaan_bonus', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('NoPenerimaanBonus', 15);
            $table->date('TglPenerimaan')->nullable();
            $table->string('KodeSupplier', 5)->nullable();
            $table->string('NoFaktur', 50)->nullable();
            $table->string('Status', 50)->nullable();

            $table->primary(['KodeDivisi', 'NoPenerimaanBonus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_penerimaan_bonus');
    }
};
