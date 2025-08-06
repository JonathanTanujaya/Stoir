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
        Schema::create('stok_minimum', function (Blueprint $table) {
            $table->string('KodeBarang', 30)->nullable();
            $table->date('Tanggal')->nullable();
            $table->integer('Stok')->nullable();
            $table->integer('StokMin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_minimum');
    }
};
