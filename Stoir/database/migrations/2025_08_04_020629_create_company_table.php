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
        Schema::create('company', function (Blueprint $table) {
            $table->string('CompanyName', 100)->nullable();
            $table->string('Alamat', 500)->nullable();
            $table->string('Kota', 100)->nullable();
            $table->string('AN', 50)->nullable();
            $table->string('Telp', 50)->nullable();
            $table->string('NPWP', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
