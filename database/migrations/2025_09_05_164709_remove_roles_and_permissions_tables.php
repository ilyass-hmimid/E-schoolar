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
        // Suppression des tables liées aux rôles et permissions
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette opération est irréversible car nous avons supprimé les tables
        // Il n'est pas possible de recréer automatiquement la structure complète des rôles et permissions
    }
};
