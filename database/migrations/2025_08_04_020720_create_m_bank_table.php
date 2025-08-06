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
        Schema::create('m_bank', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('KodeBank', 5);
            $table->string('NamaBank', 50)->nullable();
            $table->boolean('Status')->nullable();

            $table->primary(['KodeDivisi', 'KodeBank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_bank');
    }
};
