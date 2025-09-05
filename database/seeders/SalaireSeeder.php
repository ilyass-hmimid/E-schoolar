<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Salaire;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des salaires
        DB::table('salaires')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Récupérer les professeurs
        $professeurs = User::whereHas('roles', function($q) {
            $q->where('name', 'professeur');
        })->get();

        if ($professeurs->isEmpty()) {
            $this->command->error('Aucun professeur trouvé. Veuillez d\'abord créer des comptes professeurs.');
            return;
        }

        $anneeEnCours = date('Y');
        $moisDebutAnneeScolaire = 9; // Septembre
        $moisActuel = (int)date('m');
        
        // Si on est avant septembre, on prend les mois de l'année scolaire précédente
        $anneeScolaireDebut = $moisActuel < $moisDebutAnneeScolaire ? $anneeEnCours - 1 : $anneeEnCours;
        
        $statuts = ['en_attente', 'paye', 'annule'];
        
        foreach ($professeurs as $professeur) {
            // Générer des salaires pour les 12 derniers mois
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::now()->subMonths($i);
                $moisAnnee = $date->format('Y-m-01');
                
                // Ne générer que pour les mois de l'année scolaire en cours
                $anneeMois = (int)$date->format('Y');
                $mois = (int)$date->format('m');
                
                if ($anneeMois < $anneeScolaireDebut || ($anneeMois === $anneeScolaireDebut && $mois < $moisDebutAnneeScolaire)) {
                    continue;
                }
                
                $salaireBase = rand(5000, 10000);
                $prime = rand(0, 2000);
                $retenues = rand(0, 1000);
                $netAPayer = $salaireBase + $prime - $retenues;
                $statut = $statuts[array_rand($statuts)];
                
                Salaire::create([
                    'professeur_id' => $professeur->id,
                    'mois_annee' => $moisAnnee,
                    'salaire_base' => $salaireBase,
                    'prime' => $prime,
                    'retenues' => $retenues,
                    'net_a_payer' => $netAPayer,
                    'statut' => $statut,
                    'date_paiement' => $statut === 'paye' ? $date->copy()->addDays(rand(1, 5))->format('Y-m-d') : null,
                    'notes' => $statut === 'annule' ? 'Paiement annulé pour cause de congé sans solde' : null,
                ]);
            }
        }
        
        $this->command->info('Salaires générés avec succès !');
    }
}
