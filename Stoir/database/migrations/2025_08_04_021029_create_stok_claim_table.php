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
        Schema::create('stok_claim', function (Blueprint $table) {
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('StokClaim')->nullable();
            $table->decimal('Modal', 19, 4)->nullable();
            $table->bigIncrements('ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_claim');
    }
};
