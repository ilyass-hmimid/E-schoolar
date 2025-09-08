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
            // Ajout des champs manquants
            $table->decimal('prix_trimestriel', 10, 2)->nullable()->after('prix_mensuel');
            $table->string('icone', 50)->nullable()->after('couleur');
            $table->boolean('est_fixe')->default(false)->after('est_active');
            
            // Mise à jour du champ niveau pour utiliser les nouveaux codes
            $table->string('niveau', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            $table->dropColumn(['prix_trimestriel', 'icone', 'est_fixe']);
            // Revenir au type de champ précédent pour niveau si nécessaire
            $table->string('niveau')->change();
        });
    }
};
