<?php

namespace App\Services;

use App\Models\User;
use App\Models\Paiement;
use App\Models\Notification as NotificationModel;
use App\Enums\RoleType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Envoyer une notification de retard de paiement
     *
     * @param  \App\Models\User  $eleve
     * @param  int  $joursRetard
     * @param  float  $montantDu
     * @param  array  $matieres
     * @return bool
     */
    public function notifyPaiementRetard($eleve, $joursRetard, $montantDu, $matieres)
    {
        try {
            // Vérifier si une notification similaire a déjà été envoyée aujourd'hui
            $today = now()->startOfDay();
            $exists = NotificationModel::where('eleve_id', $eleve->id)
                ->where('type', NotificationModel::TYPE_PAIEMENT_RETARD)
                ->whereDate('created_at', $today)
                ->exists();
            
            if ($exists) {
                Log::info("Une notification de retard a déjà été envoyée aujourd'hui pour l'élève ID: {$eleve->id}");
                return false;
            }
            
            // Créer la notification
            NotificationModel::createPaiementRetardNotification($eleve, $joursRetard, $montantDu, $matieres);
            
            Log::info("Notification de retard de paiement envoyée pour l'élève ID: {$eleve->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification de retard de paiement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une notification de paiement effectué
     *
     * @param  \App\Models\Paiement  $paiement
     * @return bool
     */
    public function notifyPaiementEffectue($paiement)
    {
        try {
            // Créer la notification
            NotificationModel::createPaiementEffectueNotification($paiement);
            
            Log::info("Notification de paiement effectué envoyée pour le paiement ID: {$paiement->id}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification de paiement effectué: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marquer une notification comme lue
     *
     * @param  int  $notificationId
     * @param  int  $userId
     * @return bool
     */
    public function markAsRead($notificationId, $userId)
    {
        try {
            $notification = NotificationModel::where('id', $notificationId)
                ->where('user_id', $userId)
                ->first();
                
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Erreur lors du marquage de la notification comme lue: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère les notifications non lues d'un utilisateur
     *
     * @param  int  $userId
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        return NotificationModel::with('eleve')
            ->where('user_id', $userId)
            ->where('status', NotificationModel::STATUS_NON_LU)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Compte le nombre de notifications non lues pour un utilisateur
     *
     * @param  int  $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return NotificationModel::where('user_id', $userId)
            ->where('status', NotificationModel::STATUS_NON_LU)
            ->count();
    }
    
    /**
     * Vérifier les paiements en retard et envoyer des notifications
     * Cette méthode doit être planifiée pour s'exécuter quotidiennement
     *
     * @return void
     */
    public function checkRetardsPaiement()
    {
        try {
            $today = now();
            $dateLimite = $today->copy()->subDays(config('app.paiement.jours_avant_rappel', 5));
            
            // Récupérer les paiements en retard
            $paiementsEnRetard = Paiement::where('statut', 'en_attente')
                ->whereDate('date_limite', '<=', $dateLimite)
                ->with(['etudiant', 'matiere'])
                ->get()
                ->groupBy('etudiant_id');
            
            foreach ($paiementsEnRetard as $eleveId => $paiements) {
                $eleve = $paiements->first()->etudiant;
                $joursRetard = $today->diffInDays($paiements->min('date_limite'));
                $montantTotal = $paiements->sum('montant');
                $matieres = $paiements->pluck('matiere.nom')->filter()->unique()->toArray();
                
                $this->notifyPaiementRetard($eleve, $joursRetard, $montantTotal, $matieres);
            }
            
            Log::info("Vérification des retards de paiement terminée. " . count($paiementsEnRetard) . " élèves en retard.");
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification des retards de paiement: " . $e->getMessage());
        }
    }
    
    /**
     * Envoyer une notification de paiement
     *
     * @param  \App\Models\Paiement  $paiement
     * @param  string  $type
     * @return bool
     */
    public function sendPaymentNotification($paiement, $type = 'success')
    {
        try {
            if ($type === 'success') {
                return $this->notifyPaiementEffectue($paiement);
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification de paiement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une notification de note
     *
     * @param  \App\Models\Grade  $grade
     * @return bool
     */
    public function sendGradeNotification($grade): bool
    {
        try {
            $student = $grade->student;
            $parent = $student->parent;
            
            $notification = new GradeNotification($grade);
            
            // Notifier l'étudiant
            $student->user->notify($notification);
            
            // Notifier le parent si configuré
            if ($parent && $parent->receive_grade_notifications) {
                $parent->user->notify($notification);
            }
            
            // Notifier le professeur si la note est en dessous de la moyenne
            if ($grade->grade < $grade->evaluation->passing_grade) {
                $grade->evaluation->course->teacher->user->notify(
                    new GradeNotification($grade, 'low_grade')
                );
            }
            
            Log::info("Notification de note envoyée pour l'étudiant ID: {$student->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification de note: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une notification système personnalisée
     *
     * @param  User|Collection  $users
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  array  $data
     * @return bool
     */
    public function sendSystemNotification($users, string $title, string $message, string $type = 'info', array $data = []): bool
    {
        try {
            $notification = new SystemNotification($title, $message, $type, $data);
            
            if ($users instanceof User) {
                $users = collect([$users]);
            }
            
            Notification::send($users, $notification);
            
            Log::info("Notification système envoyée à {$users->count()} utilisateurs: {$title}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification système: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marquer une notification comme lue
     *
     * @param  string  $notificationId
     * @param  User  $user
     * @return bool
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        try {
            $notification = $user->unreadNotifications()->findOrFail($notificationId);
            $notification->markAsRead();
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors du marquage de la notification comme lue: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marquer toutes les notifications comme lues
     *
     * @param  User  $user
     * @return bool
     */
    public function markAllAsRead(User $user): bool
    {
        try {
            $user->unreadNotifications->markAsRead();
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors du marquage de toutes les notifications comme lues: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les notifications non lues d'un utilisateur
     *
     * @param  User  $user
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications(User $user, int $limit = 10)
    {
        return $user->unreadNotifications()
            ->latest()
            ->limit($limit)
            ->get();
    }
    
    /**
     * Envoyer un rappel de paiement
     *
     * @param  \App\Models\Student  $student
     * @param  float  $amount
     * @param  string  $dueDate
     * @return bool
     */
    public function sendPaymentReminder($student, float $amount, string $dueDate): bool
    {
        try {
            $user = $student->user;
            $parent = $student->parent;
            
            $notification = new PaymentReminderNotification($amount, $dueDate);
            
            // Envoyer à l'étudiant
            $user->notify($notification);
            
            // Envoyer au parent si disponible
            if ($parent) {
                $parent->user->notify($notification);
            }
            
            // Envoyer une copie aux administrateurs
            $admins = User::role('admin')->get();
            Notification::send($admins, $notification);
            
            Log::info("Rappel de paiement envoyé à l'étudiant ID: {$student->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi du rappel de paiement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une notification de bienvenue
     *
     * @param  User  $user
     * @param  string  $password
     * @return bool
     */
    public function sendWelcomeNotification(User $user, string $password): bool
    {
        try {
            $user->notify(new WelcomeNotification($password));
            
            Log::info("Notification de bienvenue envoyée à l'utilisateur ID: {$user->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification de bienvenue: " . $e->getMessage());
            return false;
        }
    }
}
