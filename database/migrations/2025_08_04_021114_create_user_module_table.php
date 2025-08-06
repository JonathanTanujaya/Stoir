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
        Schema::create('user_module', function (Blueprint $table) {
            $table->string('KodeDivisi', 5)->nullable();
            $table->string('Username', 50)->nullable();
            $table->string('BtID', 500)->nullable();
            $table->boolean('Modal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_module');
    }
};
