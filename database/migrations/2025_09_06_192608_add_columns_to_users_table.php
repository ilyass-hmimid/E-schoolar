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
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'niveau_id')) {
                $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'filiere_id')) {
                $table->foreignId('filiere_id')->nullable()->constrained('filieres')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('users', 'somme_a_payer')) {
                $table->decimal('somme_a_payer', 10, 2)->default(0)->after('filiere_id');
            }
            
            if (!Schema::hasColumn('users', 'date_debut')) {
                $table->date('date_debut')->nullable()->after('somme_a_payer');
            }
            
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('eleve')->after('date_debut');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['niveau_id']);
            $table->dropForeign(['filiere_id']);
            $table->dropColumn([
                'phone',
                'address',
                'niveau_id',
                'filiere_id',
                'somme_a_payer',
                'date_debut',
                'role',
                'is_active'
            ]);
        });
    }
};
