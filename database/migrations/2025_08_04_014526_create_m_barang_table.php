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
        Schema::create('m_barang', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeBarang', 30);
            $table->string('NamaBarang', 70)->nullable();
            $table->string('KodeKategori', 10)->nullable();
            $table->decimal('HargaList', 19, 4)->nullable();
            $table->decimal('HargaJual', 19, 4)->nullable();
            $table->decimal('HargaList2', 19, 4)->nullable();
            $table->decimal('HargaJual2', 19, 4)->nullable();
            $table->string('Satuan', 20)->nullable();
            $table->float('Disc1')->nullable();
            $table->float('Disc2')->nullable();
            $table->string('merk', 50)->nullable();
            $table->string('Barcode', 8)->nullable();
            $table->boolean('status')->nullable();
            $table->string('Lokasi', 50)->nullable();
            $table->integer('StokMin')->nullable();
            $table->boolean('Checklist')->nullable();

            $table->primary(['KodeDivisi', 'KodeBarang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_barang');
    }
};
