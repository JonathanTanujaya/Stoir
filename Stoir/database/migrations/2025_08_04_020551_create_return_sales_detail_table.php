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
        Schema::create('return_sales_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('NoRetur', 15)->nullable();
            $table->string('NoInvoice', 15)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('QtyRetur')->nullable();
            $table->decimal('HargaNett', 19, 4)->nullable();
            $table->bigIncrements('ID');
            $table->string('Status', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_sales_detail');
    }
};
