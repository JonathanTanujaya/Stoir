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
        Schema::create('m_coa', function (Blueprint $table) {
            $table->string('KodeCOA', 15);
            $table->string('NamaCOA', 100)->nullable();
            $table->string('SaldoNormal', 50)->nullable();

            $table->primary(['KodeCOA']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_coa');
    }
};
