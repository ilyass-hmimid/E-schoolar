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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description')->nullable();
            $table->integer('nombre_heures');
            $table->decimal('prix', 10, 2);
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table pivot pour la relation many-to-many entre packs et matières
        Schema::create('matiere_pack', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matiere_id')->constrained('Matiere', 'id')->onDelete('cascade');
            $table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
            $table->integer('nombre_heures_par_matiere')->default(0);
            $table->timestamps();
            
            // Clé unique pour éviter les doublons
            $table->unique(['matiere_id', 'pack_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matiere_pack');
        Schema::dropIfExists('packs');
    }
};
