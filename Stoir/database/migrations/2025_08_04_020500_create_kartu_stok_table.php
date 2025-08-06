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
        Schema::create('kartu_stok', function (Blueprint $table) {
            $table->bigIncrements('urut');
            $table->string('KodeDivisi', 4)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->string('No_Ref', 15)->nullable();
            $table->dateTime('TglProses')->nullable();
            $table->string('Tipe', 50)->nullable();
            $table->integer('Increase')->nullable();
            $table->integer('Decrease')->nullable();
            $table->decimal('Harga_Debet', 19, 4)->nullable();
            $table->decimal('Harga_Kredit', 19, 4)->nullable();
            $table->integer('Qty')->nullable();
            $table->decimal('HPP', 19, 4)->nullable();

            $table->primary(['urut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_stok');
    }
};
