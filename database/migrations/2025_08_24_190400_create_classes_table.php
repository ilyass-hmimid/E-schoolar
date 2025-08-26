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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('code_classe')->unique()->comment('Code unique de la classe');
            $table->string('nom');
            $table->string('niveau')->comment('Ex: CP, CE1, CE2, etc.');
            $table->string('annee_scolaire')->comment('Ex: 2023-2024');
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('professeur_principal_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('effectif_max')->default(30);
            $table->text('description')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            // Index pour les recherches courantes
            $table->index('code_classe');
            $table->index('niveau');
            $table->index('annee_scolaire');
            $table->index('filiere_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
