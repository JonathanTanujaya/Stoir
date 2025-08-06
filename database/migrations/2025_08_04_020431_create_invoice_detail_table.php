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
        Schema::create('invoice_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoInvoice', 15)->nullable();
            $table->string('KodeBarang', 50)->nullable();
            $table->integer('QtySupply')->nullable();
            $table->decimal('HargaJual', 19, 4)->nullable();
            $table->string('Jenis', 50)->nullable();
            $table->float('Diskon1')->nullable();
            $table->float('Diskon2')->nullable();
            $table->decimal('HargaNett', 19, 4)->nullable();
            $table->string('Status', 50)->nullable();
            $table->bigIncrements('ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_detail');
    }
};
