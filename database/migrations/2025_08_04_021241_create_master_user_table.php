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
        Schema::create('master_user', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('Username', 50);
            $table->string('Nama', 50)->nullable();
            $table->string('Password', 50)->nullable();

            $table->primary(['KodeDivisi', 'Username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_user');
    }
};
