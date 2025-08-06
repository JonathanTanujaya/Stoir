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
        Schema::create('d_trans', function (Blueprint $table) {
            $table->string('KodeTrans', 30)->nullable();
            $table->string('KodeCOA', 15)->nullable();
            $table->string('SaldoNormal', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_trans');
    }
};
