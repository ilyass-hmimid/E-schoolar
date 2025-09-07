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
            // Suppression des champs inutiles
            $table->dropColumn(['email_verified_at', 'remember_token']);
            
            // Ajout des champs nécessaires
            $table->enum('role', ['admin', 'professeur', 'eleve'])->default('eleve');
            $table->enum('status', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->date('date_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->decimal('pourcentage_remuneration', 5, 2)->nullable()->comment('Pour les professeurs uniquement');
            $table->date('date_embauche')->nullable()->comment('Pour les professeurs');
            $table->string('cne')->nullable()->comment('Pour les élèves');
            $table->string('nom_pere')->nullable()->comment('Pour les élèves');
            $table->string('telephone_pere', 20)->nullable()->comment('Pour les élèves');
            $table->string('nom_mere')->nullable()->comment('Pour les élèves');
            $table->string('telephone_mere', 20)->nullable()->comment('Pour les élèves');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            
            $table->dropColumn([
                'role',
                'status',
                'date_naissance',
                'adresse',
                'telephone',
                'pourcentage_remuneration',
                'date_embauche',
                'cne',
                'nom_pere',
                'telephone_pere',
                'nom_mere',
                'telephone_mere',
            ]);
            
            $table->dropSoftDeletes();
        });
    }
};
