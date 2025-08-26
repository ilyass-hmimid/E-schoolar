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
            // Ajouter uniquement les colonnes manquantes
            if (!Schema::hasColumn('matieres', 'coefficient')) {
                $table->integer('coefficient')->default(1)->after('type');
            }
            
            if (!Schema::hasColumn('matieres', 'nombre_heures')) {
                $table->integer('nombre_heures')->default(1)->after('coefficient');
            }
            
            if (!Schema::hasColumn('matieres', 'prix_mensuel')) {
                $table->decimal('prix_mensuel', 10, 2)->default(0)->after('nombre_heures');
            }
            
            if (!Schema::hasColumn('matieres', 'commission_prof')) {
                $table->decimal('commission_prof', 10, 2)->default(0)->after('prix_mensuel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matieres', function (Blueprint $table) {
            // Supprimer uniquement les colonnes que nous avons ajoutées
            $columnsToDrop = [];
            
            if (Schema::hasColumn('matieres', 'coefficient')) {
                $columnsToDrop[] = 'coefficient';
            }
            
            if (Schema::hasColumn('matieres', 'nombre_heures')) {
                $columnsToDrop[] = 'nombre_heures';
            }
            
            if (Schema::hasColumn('matieres', 'prix_mensuel')) {
                $columnsToDrop[] = 'prix_mensuel';
            }
            
            if (Schema::hasColumn('matieres', 'commission_prof')) {
                $columnsToDrop[] = 'commission_prof';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
