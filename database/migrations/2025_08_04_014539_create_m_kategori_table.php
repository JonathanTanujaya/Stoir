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
        Schema::create('m_kategori', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeKategori', 10);
            $table->string('Kategori', 50)->nullable();
            $table->boolean('Status')->nullable();

            $table->primary(['KodeDivisi', 'KodeKategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kategori');
    }
};
