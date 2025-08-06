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
        Schema::create('opname_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 4)->nullable();
            $table->string('NoOpname', 15)->nullable();
            $table->string('KodeBarang', 30)->nullable();
            $table->integer('Qty')->nullable();
            $table->decimal('Modal', 19, 4)->nullable();
            $table->bigIncrements('ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opname_detail');
    }
};
