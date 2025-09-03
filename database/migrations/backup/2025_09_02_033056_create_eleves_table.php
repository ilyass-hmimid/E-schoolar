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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->date('date_naissance');
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('pays')->default('Maroc');
            $table->string('nom_pere')->nullable();
            $table->string('profession_pere')->nullable();
            $table->string('telephone_pere')->nullable();
            $table->string('nom_mere')->nullable();
            $table->string('profession_mere')->nullable();
            $table->string('telephone_mere')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
