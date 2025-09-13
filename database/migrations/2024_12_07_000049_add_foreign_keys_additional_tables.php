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
        // All foreign keys are now handled by the main SQL script.
        // This migration can be used for future alterations.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The down method in the main SQL migration will handle dropping tables.
    }
};