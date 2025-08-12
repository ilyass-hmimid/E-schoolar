<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AbsenceNotification;
use App\Notifications\PaymentNotification;
use App\Notifications\GradeNotification;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Envoyer une notification d'absence
     *
     * @param  \App\Models\Absence  $absence
     * @param  array  $channels
     * @return void
     */
    public function sendAbsenceNotification($absence, array $channels = ['mail', 'database'])
    {
        try {
            $student = $absence->student;
            $parent = $student->parent;
            
            // Notifier les administrateurs
            $admins = User::role('admin')->get();
            Notification::send($admins, new AbsenceNotification($absence, 'admin'));
            
            // Notifier le professeur
            if ($absence->course && $absence->course->teacher) {
                $absence->course->teacher->user->notify(
                    new AbsenceNotification($absence, 'teacher')
                );
            }
            
            // Notifier le parent si configuré
            if (config('notifications.settings.absence.notify_parent') && $parent) {
                $parent->user->notify(
                    (new AbsenceNotification($absence, 'parent'))->onQueue('notifications')
                );
            }
            
            // Journalisation
            Log::info("Notification d'absence envoyée pour l'étudiant ID: {$student->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la notification d'absence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer une notification de paiement
     *
     * @param  \App\Models\Payment  $payment
     * @param  string  $type
     * @return bool
     */
    public function sendPaymentNotification($payment, string $type = 'confirmation'): bool
    {
        try {
            $student = $payment->student;
            
            $notification = new PaymentNotification($payment, $type);
            
            // Notifier l'étudiant
            $student->user->notify($notification);
            
            // Notifier les administrateurs pour les paiements importants
            if ($payment->amount > 5000) {
                $admins = User::role('admin')->get();
                Notification::send($admins, $notification);
            }
            
            Log::info("Notification de paiement {$type} envoyée pour le paiement ID: {$payment->id}");
            
            return true;
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
