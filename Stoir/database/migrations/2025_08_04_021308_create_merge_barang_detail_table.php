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
        Schema::create('merge_barang_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 4)->nullable();
            $table->string('NoCreate', 15)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('Qty')->nullable();
            $table->decimal('Modal', 19, 4)->nullable();
            $table->increments('ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merge_barang_detail');
    }
};
