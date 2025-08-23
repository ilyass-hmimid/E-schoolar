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
            $table->integer('role')->after('email')->default(4); // 4 = ELEVE par défaut
            $table->string('phone')->nullable()->after('role');
            $table->text('address')->nullable()->after('phone');
            $table->string('parent_phone')->nullable()->after('address');
            $table->string('parent_email')->nullable()->after('parent_phone');
            $table->unsignedBigInteger('niveau_id')->nullable()->after('parent_email');
            $table->unsignedBigInteger('filiere_id')->nullable()->after('niveau_id');
            $table->decimal('somme_a_payer', 10, 2)->default(0)->after('filiere_id');
            $table->date('date_debut')->nullable()->after('somme_a_payer');
            $table->boolean('is_active')->default(true)->after('date_debut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'parent_phone', 'parent_email', 'niveau_id', 'filiere_id', 'somme_a_payer', 'date_debut', 'is_active']);
        });
    }
};
