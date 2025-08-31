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
        // Ensure role column is INT NOT NULL with default value 1
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role')
                  ->default(1)
                  ->change();
                  
            // Ensure somme_a_payer is DECIMAL(10,2) with default 0.00
            $table->decimal('somme_a_payer', 10, 2)
                  ->default(0.00)
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert changes if needed
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role')
                  ->default(4) // Revert to original default if different
                  ->change();
                  
            $table->decimal('somme_a_payer', 10, 2)
                  ->default(0.00)
                  ->change();
        });
    }
};
