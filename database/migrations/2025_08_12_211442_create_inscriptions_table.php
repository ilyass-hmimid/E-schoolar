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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('IdEtudiant')->constrained('users')->onDelete('cascade');
            $table->foreignId('IdFil')->constrained('filieres')->onDelete('cascade');
            $table->date('DateInsc');
            $table->decimal('Montant', 10, 2);
            $table->string('ModePaiement', 50);
            $table->string('Statut', 20)->default('en_attente');
            $table->text('Commentaires')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
