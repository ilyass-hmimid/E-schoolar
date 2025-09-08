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
            // Ajout des nouveaux champs
            $table->enum('niveau', ['primaire', 'college', 'lycee'])->after('description');
            $table->decimal('prix_mensuel', 10, 2)->default(0)->after('niveau');
            $table->string('couleur', 20)->default('#3498db')->after('prix_mensuel');
            $table->boolean('est_active')->default(true)->after('couleur');
            
            // Suppression du champ code qui n'est plus nécessaire
            $table->dropColumn('code');
            
            // Ajout d'un index sur le niveau pour de meilleures performances
            $table->index('niveau');
            $table->index('est_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            // Suppression des champs ajoutés
            $table->dropColumn(['niveau', 'prix_mensuel', 'couleur', 'est_active']);
            
            // Recréation du champ code
            $table->string('code')->unique()->after('nom');
            
            // Suppression des index
            $table->dropIndex(['niveau']);
            $table->dropIndex(['est_active']);
        });
    }
};
