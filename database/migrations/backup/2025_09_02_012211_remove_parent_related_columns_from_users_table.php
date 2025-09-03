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
        Schema::table('users', function (Blueprint $table) {
            // Suppression des colonnes liées aux parents si elles existent
            $columns = [
                'parent_name',
                'parent_phone',
                'parent_email',
                'parent_profession',
                'parent_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recréation des colonnes en cas de rollback
            $table->string('parent_name')->nullable()->after('address');
            $table->string('parent_phone', 20)->nullable()->after('parent_name');
            $table->string('parent_email')->nullable()->after('parent_phone');
            $table->string('parent_profession', 100)->nullable()->after('parent_email');
            $table->unsignedBigInteger('parent_id')->nullable()->after('parent_profession');
        });
    }
};
