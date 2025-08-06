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
        Schema::create('m_tt', function (Blueprint $table) {
            $table->string('NoTT', 15)->nullable();
            $table->date('Tanggal')->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->string('Keterangan', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_tt');
    }
};
