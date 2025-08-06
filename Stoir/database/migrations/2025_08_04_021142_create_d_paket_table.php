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
        Schema::create('d_paket', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodePaket', 10);
            $table->string('KodeKategori', 30);
            $table->integer('QtyMin')->nullable();
            $table->integer('QtyMax')->nullable();
            $table->float('Diskon1')->nullable();
            $table->float('Diskon2')->nullable();
            $table->bigIncrements('id');

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_paket');
    }
};
