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
        Schema::table('matieres', function (Blueprint $table) {
            // Suppression des champs inutiles
            $table->dropColumn(['code']);
            
            // Ajout des champs nécessaires
            $table->decimal('prix_mensuel', 10, 2)->default(0);
            $table->string('couleur', 20)->default('#3b82f6');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->string('code')->unique()->after('id');
            
            $table->dropColumn([
                'prix_mensuel',
                'couleur',
            ]);
            
            $table->dropSoftDeletes();
        });
    }
};
