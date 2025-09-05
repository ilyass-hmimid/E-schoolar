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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('info'); // info, warning, success, error
            $table->boolean('est_lu')->default(false);
            $table->timestamp('date_lecture')->nullable();
            $table->string('lien')->nullable();
            $table->json('donnees')->nullable(); // Pour stocker des données supplémentaires
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
