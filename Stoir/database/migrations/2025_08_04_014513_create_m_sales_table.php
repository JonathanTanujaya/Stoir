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
        Schema::create('m_sales', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeSales', 5);
            $table->string('NamaSales', 50)->nullable();
            $table->string('Alamat', 500)->nullable();
            $table->string('NoHP', 20)->nullable();
            $table->decimal('Target', 19, 4)->nullable();
            $table->boolean('Status')->nullable();

            $table->primary(['KodeDivisi', 'KodeSales']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_sales');
    }
};
