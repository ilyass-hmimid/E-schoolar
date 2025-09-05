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
        
        // Créer des notifications pour chaque utilisateur
        foreach ($users as $user) {
            // Nombre de notifications à créer pour cet utilisateur (entre 3 et 10)
            $nombreNotifications = rand(3, 10);
            
            for ($i = 0; $i < $nombreNotifications; $i++) {
                $sujets = [
                    'Paiement en retard',
                    'Absence non justifiée',
                    'Nouvelle note disponible',
                    'Changement d\'emploi du temps',
                    'Annonce importante',
                    'Rappel de rendez-vous',
                    'Information administrative',
                ];
                
                $sujet = $sujets[array_rand($sujets)];
                $type = $this->getNotificationType($sujet);
                $estLue = (bool)rand(0, 1);
                
                // Créer la notification avec les champs existants
                Notification::create([
                    'user_id' => $user->id,
                    'titre' => $sujet,
                    'contenu' => $this->genererMessage($sujet, $user->name),
                    'type' => $type,
                    'est_lu' => $estLue,
                    'date_lecture' => $estLue ? now() : null,
                    'donnees' => ['sujet' => $sujet],
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now(),
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
            'Paiement en retard' => "Cher(e) $nomUtilisateur, votre paiement est en retard. Merci de régulariser votre situation au plus vite.",
            'Absence non justifiée' => "Cher(e) $nomUtilisateur, vous avez une absence non justifiée. Veuillez fournir une justification.",
            'Nouvelle note disponible' => "Cher(e) $nomUtilisateur, une nouvelle note a été publiée. Connectez-vous pour la consulter.",
            'Changement d\'emploi du temps' => "Cher(e) $nomUtilisateur, des modifications ont été apportées à votre emploi du temps.",
            'Annonce importante' => "Cher(e) $nomUtilisateur, une annonce importante a été publiée. Veuillez en prendre connaissance.",
            'Rappel de rendez-vous' => "Cher(e) $nomUtilisateur, vous avez un rendez-vous prévu demain à 8h00. Merci d'être à l'heure.",
            'Information administrative' => "Cher(e) $nomUtilisateur, veuillez prendre connaissance de cette information administrative importante.",
        ];
        
        return $messages[$sujet] ?? "Cher(e) $nomUtilisateur, vous avez une nouvelle notification.";
    }
    
    /**
     * Get notification type based on subject
     */
    private function getNotificationType(string $sujet): string
    {
        $types = [
            'Paiement en retard' => TypeNotification::PAIEMENT->value,
            'Absence non justifiée' => TypeNotification::ABSENCE->value,
            'Nouvelle note disponible' => TypeNotification::AUTRE->value,
            'Changement d\'emploi du temps' => TypeNotification::COURS->value,
            'Annonce importante' => TypeNotification::MESSAGE->value,
            'Rappel de rendez-vous' => TypeNotification::MESSAGE->value,
            'Information administrative' => TypeNotification::MESSAGE->value,
        ];
        
        return $types[$sujet] ?? TypeNotification::AUTRE->value;
    }
    
    /**
     * Générer un lien en fonction du type de notification
     */
    private function genererLien(string $type): ?string
    {
        $liens = [
            TypeNotification::PAIEMENT->value => '/paiements',
            TypeNotification::ABSENCE->value => '/absences',
            TypeNotification::COURS->value => '/cours',
            TypeNotification::MESSAGE->value => '/messages',
            TypeNotification::AUTRE->value => null,
        ];
        
        return $liens[$type] ?? null;
    }
}
