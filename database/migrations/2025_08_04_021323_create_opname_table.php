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
        Schema::create('opname', function (Blueprint $table) {
            $table->string('KodeDivisi', 4)->nullable();
            $table->string('NoOpname', 15)->nullable();
            $table->date('Tanggal')->nullable();
            $table->decimal('Total', 19, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opname');
    }
};
