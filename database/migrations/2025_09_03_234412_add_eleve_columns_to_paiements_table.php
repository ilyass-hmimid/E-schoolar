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
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('eleve_id')->nullable()->after('etudiant_id')->constrained('users');
            $table->string('type')->nullable()->after('assistant_id');
            $table->text('notes')->nullable()->after('commentaires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['eleve_id']);
            $table->dropColumn('eleve_id');
            $table->dropColumn('type');
            $table->dropColumn('notes');
        });
    }
};
