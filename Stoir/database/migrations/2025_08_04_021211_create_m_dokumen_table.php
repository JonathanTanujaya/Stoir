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
        Schema::create('m_dokumen', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeDok', 50);
            $table->string('Nomor', 15)->nullable();

            $table->primary(['KodeDivisi', 'KodeDok']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_dokumen');
    }
};
