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
        Schema::create('pack_vente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
            $table->foreignId('vente_id')->constrained('ventes')->onDelete('cascade');
            $table->decimal('prix_unitaire', 10, 2);
            $table->integer('quantite')->default(1);
            $table->decimal('prix_total', 10, 2);
            $table->timestamps();
            
            // Clé unique pour éviter les doublons
            $table->unique(['pack_id', 'vente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pack_vente');
    }
};
