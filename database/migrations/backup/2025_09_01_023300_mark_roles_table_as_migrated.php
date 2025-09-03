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
        // Cette migration ne fait rien car la table roles existe déjà
        // Elle est utilisée uniquement pour marquer la migration comme exécutée
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire car on ne veut pas supprimer la table roles
    }
};
