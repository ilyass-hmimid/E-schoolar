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
            // Vérifier et ajouter uniquement les champs manquants
            
            // Champs pour les informations personnelles
            if (!Schema::hasColumn('users', 'cni')) {
                $table->string('cni', 20)->nullable()->unique()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'cne')) {
                $table->string('cne', 20)->nullable()->unique()->after('cni');
            }
            
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom', 100)->after('name');
            }
            
            if (!Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable()->after('prenom');
            }
            
            if (!Schema::hasColumn('users', 'lieu_naissance')) {
                $table->string('lieu_naissance', 255)->nullable()->after('date_naissance');
            }
            
            if (!Schema::hasColumn('users', 'adresse')) {
                $table->string('adresse', 255)->nullable()->after('lieu_naissance');
            }
            
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 20)->nullable()->after('adresse');
            }
            
            if (!Schema::hasColumn('users', 'sexe')) {
                $table->enum('sexe', ['Homme', 'Femme'])->default('Homme')->after('telephone');
            }
            
            // Champs pour la scolarité
            if (!Schema::hasColumn('users', 'date_inscription')) {
                $table->date('date_inscription')->nullable()->after('filiere_id');
            }
            
            // Champs pour les informations des parents
            if (!Schema::hasColumn('users', 'nom_pere')) {
                $table->string('nom_pere', 100)->nullable()->after('date_inscription');
            }
            
            if (!Schema::hasColumn('users', 'profession_pere')) {
                $table->string('profession_pere', 100)->nullable()->after('nom_pere');
            }
            
            if (!Schema::hasColumn('users', 'telephone_pere')) {
                $table->string('telephone_pere', 20)->nullable()->after('profession_pere');
            }
            
            if (!Schema::hasColumn('users', 'nom_mere')) {
                $table->string('nom_mere', 100)->nullable()->after('telephone_pere');
            }
            
            if (!Schema::hasColumn('users', 'profession_mere')) {
                $table->string('profession_mere', 100)->nullable()->after('nom_mere');
            }
            
            if (!Schema::hasColumn('users', 'telephone_mere')) {
                $table->string('telephone_mere', 20)->nullable()->after('profession_mere');
            }
            
            // Autres champs
            if (!Schema::hasColumn('users', 'remarques')) {
                $table->text('remarques')->nullable()->after('telephone_mere');
            }
            
            // Supprimer les champs inutiles s'ils existent
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recréer les colonnes supprimées si elles n'existent pas
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
            
            // Supprimer les colonnes ajoutées par cette migration
            $columnsToDrop = [];
            
            if (Schema::hasColumn('users', 'cni')) {
                $columnsToDrop[] = 'cni';
            }
            
            if (Schema::hasColumn('users', 'cne')) {
                $columnsToDrop[] = 'cne';
            }
            
            if (Schema::hasColumn('users', 'prenom')) {
                $columnsToDrop[] = 'prenom';
            }
            
            if (Schema::hasColumn('users', 'date_naissance')) {
                $columnsToDrop[] = 'date_naissance';
            }
            
            if (Schema::hasColumn('users', 'lieu_naissance')) {
                $columnsToDrop[] = 'lieu_naissance';
            }
            
            if (Schema::hasColumn('users', 'adresse')) {
                $columnsToDrop[] = 'adresse';
            }
            
            if (Schema::hasColumn('users', 'telephone')) {
                $columnsToDrop[] = 'telephone';
            }
            
            if (Schema::hasColumn('users', 'sexe')) {
                $columnsToDrop[] = 'sexe';
            }
            
            if (Schema::hasColumn('users', 'date_inscription')) {
                $columnsToDrop[] = 'date_inscription';
            }
            
            if (Schema::hasColumn('users', 'nom_pere')) {
                $columnsToDrop[] = 'nom_pere';
            }
            
            if (Schema::hasColumn('users', 'profession_pere')) {
                $columnsToDrop[] = 'profession_pere';
            }
            
            if (Schema::hasColumn('users', 'telephone_pere')) {
                $columnsToDrop[] = 'telephone_pere';
            }
            
            if (Schema::hasColumn('users', 'nom_mere')) {
                $columnsToDrop[] = 'nom_mere';
            }
            
            if (Schema::hasColumn('users', 'profession_mere')) {
                $columnsToDrop[] = 'profession_mere';
            }
            
            if (Schema::hasColumn('users', 'telephone_mere')) {
                $columnsToDrop[] = 'telephone_mere';
            }
            
            if (Schema::hasColumn('users', 'remarques')) {
                $columnsToDrop[] = 'remarques';
            }
            
            // Supprimer les colonnes en une seule fois si nécessaire
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
