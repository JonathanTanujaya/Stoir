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
        Schema::create('m_supplier', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeSupplier', 5);
            $table->string('NamaSupplier', 50)->nullable();
            $table->string('Alamat', 100)->nullable();
            $table->string('Telp', 50)->nullable();
            $table->string('contact', 50)->nullable();
            $table->boolean('status')->nullable();

            $table->primary(['KodeDivisi', 'KodeSupplier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_supplier');
    }
};
