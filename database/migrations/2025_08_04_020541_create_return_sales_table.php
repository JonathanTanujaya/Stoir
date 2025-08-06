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
        Schema::create('return_sales', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoRetur', 15)->nullable();
            $table->date('TglRetur')->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->decimal('Total', 19, 4)->nullable();
            $table->decimal('SisaRetur', 19, 4)->nullable();
            $table->string('Keterangan', 200)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('TT', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_sales');
    }
};
