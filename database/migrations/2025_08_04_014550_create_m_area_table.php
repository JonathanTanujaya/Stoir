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
        Schema::create('m_area', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeArea', 10);
            $table->string('Area', 50)->nullable();
            $table->boolean('status')->nullable();

            $table->primary(['KodeDivisi', 'KodeArea']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_area');
    }
};
