<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Salaire;
use App\Enums\RoleType;
use App\Notifications\AbsenceNotification;
use App\Notifications\PaymentNotification;
use App\Notifications\PaymentReminderNotification;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Affiche la liste des notifications de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Marquer comme lues si demandé
        if ($request->has('mark_read')) {
            $user->unreadNotifications->markAsRead();
        }

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marque toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Supprime une notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Envoie une notification d'absence aux parents
     */
    public function notifierAbsence(Absence $absence)
    {
        try {
            DB::beginTransaction();

            $etudiant = $absence->etudiant;
            
            // Vérifier si l'étudiant a des informations de contact parent
            if (!$etudiant->parent_email && !$etudiant->parent_phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune information de contact parent disponible'
                ], 400);
            }

            // Créer la notification
            $notification = new AbsenceNotification($absence);
            
            // Envoyer par email si disponible
            if ($etudiant->parent_email) {
                try {
                    Mail::to($etudiant->parent_email)->send($notification);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email absence: ' . $e->getMessage());
                }
            }

            // Envoyer par SMS si disponible (nécessite un service SMS)
            if ($etudiant->parent_phone) {
                $this->envoyerSMS($etudiant->parent_phone, $this->genererMessageAbsence($absence));
            }

            // Notifier l'admin
            $admins = User::where('role', RoleType::ADMIN)->get();
            Notification::send($admins, $notification);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notification d\'absence envoyée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoie une notification de paiement
     */
    public function notifierPaiement(Paiement $paiement)
    {
        try {
            DB::beginTransaction();

            $etudiant = $paiement->etudiant;
            $notification = new PaymentNotification($paiement);

            // Notifier l'étudiant/parent
            if ($etudiant->email) {
                try {
                    Mail::to($etudiant->email)->send($notification);
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email paiement: ' . $e->getMessage());
                }
            }

            // Notifier l'admin
            $admins = User::where('role', RoleType::ADMIN)->get();
            Notification::send($admins, $notification);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notification de paiement envoyée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoie des rappels de paiement
     */
    public function envoyerRappelsPaiement(Request $request)
    {
        try {
            $validated = $request->validate([
                'jours_retard' => 'required|integer|min:1|max:90',
                'message_personnalise' => 'nullable|string|max:500',
            ]);

            $joursRetard = $validated['jours_retard'];
            $messagePersonnalise = $validated['message_personnalise'] ?? '';

            // Récupérer les paiements en retard
            $paiementsEnRetard = Paiement::where('statut', 'en_attente')
                ->where('date_echeance', '<', now()->subDays($joursRetard))
                ->with('etudiant')
                ->get();

            $notificationsEnvoyees = 0;

            foreach ($paiementsEnRetard as $paiement) {
                $notification = new PaymentReminderNotification($paiement, $messagePersonnalise);
                
                if ($paiement->etudiant->email) {
                    try {
                        Mail::to($paiement->etudiant->email)->send($notification);
                        $notificationsEnvoyees++;
                    } catch (\Exception $e) {
                        \Log::error('Erreur envoi rappel paiement: ' . $e->getMessage());
                    }
                }

                if ($paiement->etudiant->parent_phone) {
                    $this->envoyerSMS($paiement->etudiant->parent_phone, $this->genererMessageRappel($paiement, $messagePersonnalise));
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$notificationsEnvoyees} rappels de paiement envoyés",
                'notifications_envoyees' => $notificationsEnvoyees,
                'total_paiements' => $paiementsEnRetard->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des rappels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoie une notification système
     */
    public function envoyerNotificationSysteme(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'destinataires' => 'required|array',
                'destinataires.*' => 'in:admin,professeur,assistant,eleve,parent',
                'type' => 'required|in:info,warning,error,success',
                'envoyer_email' => 'boolean',
                'envoyer_sms' => 'boolean',
            ]);

            $users = collect();

            // Récupérer les utilisateurs selon les rôles
            foreach ($validated['destinataires'] as $role) {
                switch ($role) {
                    case 'admin':
                        $users = $users->merge(User::where('role', RoleType::ADMIN)->get());
                        break;
                    case 'professeur':
                        $users = $users->merge(User::where('role', RoleType::PROFESSEUR)->get());
                        break;
                    case 'assistant':
                        $users = $users->merge(User::where('role', RoleType::ASSISTANT)->get());
                        break;
                    case 'eleve':
                        $users = $users->merge(User::where('role', RoleType::ELEVE)->get());
                        break;
                    case 'parent':
                        $users = $users->merge(User::where('role', RoleType::PARENT)->get());
                        break;
                }
            }

            $users = $users->unique('id');
            $notification = new SystemNotification($validated['titre'], $validated['message'], $validated['type']);

            // Envoyer les notifications
            Notification::send($users, $notification);

            // Envoyer par email si demandé
            if ($validated['envoyer_email']) {
                foreach ($users as $user) {
                    if ($user->email) {
                        try {
                            Mail::to($user->email)->send($notification);
                        } catch (\Exception $e) {
                            \Log::error('Erreur envoi email système: ' . $e->getMessage());
                        }
                    }
                }
            }

            // Envoyer par SMS si demandé
            if ($validated['envoyer_sms']) {
                foreach ($users as $user) {
                    if ($user->phone) {
                        $this->envoyerSMS($user->phone, $this->genererMessageSysteme($validated['titre'], $validated['message']));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Notification envoyée à {$users->count()} utilisateurs",
                'destinataires' => $users->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les statistiques des notifications
     */
    public function statistiques()
    {
        $user = Auth::user();
        
        $stats = [
            'total_notifications' => $user->notifications()->count(),
            'notifications_lues' => $user->readNotifications()->count(),
            'notifications_non_lues' => $user->unreadNotifications()->count(),
            'notifications_aujourd_hui' => $user->notifications()
                ->whereDate('created_at', today())
                ->count(),
            'notifications_cette_semaine' => $user->notifications()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Génère un message SMS pour une absence
     */
    private function genererMessageAbsence(Absence $absence)
    {
        $etudiant = $absence->etudiant;
        $matiere = $absence->matiere;
        $date = $absence->date_absence->format('d/m/Y H:i');
        
        $type = match($absence->type) {
            'absence' => 'absence',
            'retard' => 'retard de ' . $absence->duree_retard . ' minutes',
            'sortie_anticipée' => 'sortie anticipée',
            default => 'absence'
        };

        return "Allo Tawjih: {$etudiant->name} a eu une {$type} en {$matiere->nom} le {$date}. Motif: {$absence->motif}";
    }

    /**
     * Génère un message SMS pour un rappel de paiement
     */
    private function genererMessageRappel(Paiement $paiement, string $messagePersonnalise = '')
    {
        $etudiant = $paiement->etudiant;
        $montant = number_format($paiement->montant, 2, ',', ' ') . ' DH';
        $dateEcheance = $paiement->date_echeance->format('d/m/Y');
        
        $message = "Allo Tawjih: Rappel - Paiement de {$montant} pour {$etudiant->name} était dû le {$dateEcheance}.";
        
        if ($messagePersonnalise) {
            $message .= " {$messagePersonnalise}";
        }
        
        return $message;
    }

    /**
     * Génère un message SMS pour une notification système
     */
    private function genererMessageSysteme(string $titre, string $message)
    {
        return "Allo Tawjih - {$titre}: {$message}";
    }

    /**
     * Envoie un SMS (méthode à adapter selon le service SMS utilisé)
     */
    private function envoyerSMS(string $numero, string $message)
    {
        // TODO: Implémenter l'envoi SMS avec un service comme Twilio, Vonage, etc.
        // Pour l'instant, on log le message
        \Log::info("SMS à envoyer à {$numero}: {$message}");
        
        // Exemple avec Twilio (nécessite l'installation du package)
        /*
        try {
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $twilio->messages->create(
                $numero,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Erreur envoi SMS: ' . $e->getMessage());
        }
        */
    }

    /**
     * Teste la configuration des notifications
     */
    public function testerConfiguration()
    {
        $tests = [
            'email' => $this->testerEmail(),
            'sms' => $this->testerSMS(),
            'database' => $this->testerDatabase(),
        ];

        return response()->json($tests);
    }

    /**
     * Teste la configuration email
     */
    private function testerEmail()
    {
        try {
            $user = Auth::user();
            $testNotification = new SystemNotification(
                'Test Email',
                'Ceci est un test de configuration email.',
                'info'
            );
            
            Mail::to($user->email)->send($testNotification);
            
            return [
                'success' => true,
                'message' => 'Email de test envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Teste la configuration SMS
     */
    private function testerSMS()
    {
        try {
            $user = Auth::user();
            $this->envoyerSMS($user->phone, 'Test SMS - Allo Tawjih');
            
            return [
                'success' => true,
                'message' => 'SMS de test envoyé (vérifiez les logs)'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Teste la configuration de la base de données
     */
    private function testerDatabase()
    {
        try {
            $user = Auth::user();
            $testNotification = new SystemNotification(
                'Test Base de données',
                'Ceci est un test de notification en base de données.',
                'info'
            );
            
            $user->notify($testNotification);
            
            return [
                'success' => true,
                'message' => 'Notification de test créée en base de données'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur base de données: ' . $e->getMessage()
            ];
        }
    }
}
