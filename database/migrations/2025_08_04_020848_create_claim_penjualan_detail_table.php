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
        Schema::create('claim_penjualan_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoClaim', 15)->nullable();
            $table->string('NoInvoice', 15)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('QtyClaim')->nullable();
            $table->bigIncrements('ID');
            $table->string('Status', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_penjualan_detail');
    }
};
