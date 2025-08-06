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
        Schema::create('m_cust_diskon_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('Qtymin')->nullable();
            $table->integer('QtyMax')->nullable();
            $table->float('diskon')->nullable();
            $table->float('Diskon1')->nullable();
            $table->float('Diskon2')->nullable();
            $table->string('jenis', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_cust_diskon_detail');
    }
};
