<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop any problematic foreign keys
        Schema::table('paiements', function (Blueprint $table) {
            // List of all possible foreign keys that might exist
            $foreignKeys = [
                'paiements_assistant_id_foreign',
                'paiements_eleve_id_foreign',
                'paiements_etudiant_id_foreign',
                'paiements_matiere_id_foreign',
                'paiements_pack_id_foreign',
                'paiements_tarif_id_foreign',
                'paiements_user_id_foreign'
            ];

            // Get the current connection
            $connection = DB::connection();
            
            // Drop foreign keys if they exist
            foreach ($foreignKeys as $fk) {
                try {
                    // Check if the foreign key exists
                    $fkExists = $connection->select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE CONSTRAINT_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'paiements' 
                        AND CONSTRAINT_NAME = ? 
                        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                    ", [$fk]);
                    
                    if ($fkExists[0]->count > 0) {
                        $table->dropForeign($fk);
                    }
                } catch (\Exception $e) {
                    // Skip if there's an error dropping the foreign key
                    continue;
                }
            }

            // Drop columns that are not needed
            $columnsToDrop = ['tarif_id', 'date_debut', 'date_fin', 'nombre_mois'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('paiements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Now modify the table structure
        Schema::table('paiements', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('paiements', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->after('id');
            }

            if (!Schema::hasColumn('paiements', 'montant_paye')) {
                $table->decimal('montant_paye', 10, 2)->after('montant')->default(0);
            }

            if (!Schema::hasColumn('paiements', 'reste')) {
                $table->decimal('reste', 10, 2)->after('montant_paye')->default(0);
            }

            // Convert statut to string if it's an enum
            if (Schema::hasColumn('paiements', 'statut')) {
                DB::statement("ALTER TABLE paiements MODIFY statut VARCHAR(20) NOT NULL DEFAULT 'en_attente'");
            } else {
                $table->string('statut', 20)->default('en_attente');
            }

            // Add check constraint if supported
            try {
                DB::statement("ALTER TABLE paiements DROP CONSTRAINT IF EXISTS chk_paiements_statut");
                DB::statement("ALTER TABLE paiements ADD CONSTRAINT chk_paiements_statut 
                    CHECK (statut IN ('en_attente', 'valide', 'annule', 'partiel'))");
            } catch (\Exception $e) {
                // Ignore if check constraints are not supported
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to implement down() for this migration
    }
};
