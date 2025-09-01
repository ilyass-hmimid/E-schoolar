<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Enums\TypeNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table des notifications
        DB::table('notifications')->truncate();
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Récupérer les utilisateurs
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('Veuillez d\'abord exécuter les seeders pour les utilisateurs.');
            return;
        }
        
        $notificationsCreees = 0;
        $types = TypeNotification::cases();
        $sujets = [
            'Réunion des parents',
            'Paiement en retard',
            'Absence non justifiée',
            'Nouvelle note disponible',
            'Changement d\'emploi du temps',
            'Annonce importante',
            'Rappel de rendez-vous',
            'Information administrative',
        ];
        
        // Créer des notifications pour chaque utilisateur
        foreach ($users as $user) {
            // Nombre de notifications à créer pour cet utilisateur (entre 3 et 10)
            $nombreNotifications = rand(3, 10);
            
            for ($i = 0; $i < $nombreNotifications; $i++) {
                $type = $types[array_rand($types)]->value;
                $sujet = $sujets[array_rand($sujets)];
                $dateCreation = now()->subDays(rand(0, 60))->subHours(rand(0, 23));
                $estLue = rand(0, 1) === 1;
                $dateLecture = $estLue ? $dateCreation->copy()->addHours(rand(1, 24)) : null;
                
                // Créer la notification
                Notification::create([
                    'user_id' => $user->id,
                    'titre' => $sujet,
                    'message' => $this->genererMessage($sujet, $user->name),
                    'type' => $type,
                    'est_lue' => $estLue,
                    'date_lecture' => $dateLecture,
                    'lien' => $this->genererLien($type),
                    'created_at' => $dateCreation,
                    'updated_at' => $dateCreation,
                ]);
                
                $notificationsCreees++;
            }
        }
        
        $this->command->info("$notificationsCreees notifications créées avec succès !");
    }
    
    /**
     * Générer un message de notification en fonction du sujet
     */
    private function genererMessage(string $sujet, string $nomUtilisateur): string
    {
        $messages = [
            'Réunion des parents' => "Cher(e) $nomUtilisateur, une réunion des parents est prévue la semaine prochaine. Veuillez consulter l'agenda pour plus de détails.",
            'Paiement en retard' => "Cher(e) $nomUtilisateur, nous vous informons que vous avez un paiement en retard. Veuillez régulariser votre situation au plus vite.",
            'Absence non justifiée' => "Cher(e) $nomUtilisateur, votre absence du " . now()->subDays(rand(1, 5))->format('d/m/Y') . " n'a pas encore été justifiée. Merci de fournir un justificatif.",
            'Nouvelle note disponible' => "Cher(e) $nomUtilisateur, une nouvelle note a été ajoutée à votre dossier. Connectez-vous pour la consulter.",
            'Changement d\'emploi du temps' => "Cher(e) $nomUtilisateur, des modifications ont été apportées à votre emploi du temps. Veuillez consulter la nouvelle version.",
            'Annonce importante' => "Cher(e) $nomUtilisateur, une annonce importante a été publiée. Veuillez en prendre connaissance.",
            'Rappel de rendez-vous' => "Cher(e) $nomUtilisateur, vous avez un rendez-vous prévu demain à " . rand(8, 17) . "h00. Merci d'être à l'heure.",
            'Information administrative' => "Cher(e) $nomUtilisateur, nous vous prions de bien vouloir prendre connaissance de l'information administrative jointe à ce message.",
        ];
        
        return $messages[$sujet] ?? "Cher(e) $nomUtilisateur, vous avez une nouvelle notification concernant : $sujet";
    }
    
    /**
     * Générer un lien en fonction du type de notification
     */
    private function genererLien(string $type): ?string
    {
        $liens = [
            TypeNotification::PAIEMENT->value => '/paiements',
            TypeNotification::ABSENCE->value => '/absences',
            TypeNotification::NOTE->value => '/notes',
            TypeNotification::EMPLOI_DU_TEMPS->value => '/emploi-du-temps',
            TypeNotification::AGENDA->value => '/agenda',
            TypeNotification::AUTRE->value => null,
        ];
        
        return $liens[$type] ?? null;
    }
}
