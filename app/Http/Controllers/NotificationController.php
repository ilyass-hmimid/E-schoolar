<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Paiement;
use App\Models\Notification as NotificationModel;
use App\Enums\RoleType;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Récupère les notifications non lues de l'utilisateur connecté
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->input('limit', 10);
        
        $notifications = $this->notificationService->getUnreadNotifications($user->id, $limit);
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $this->notificationService->getUnreadCount($user->id)
        ]);
    }

    /**
     * Marque une notification comme lue
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        $success = $this->notificationService->markAsRead($id, $request->user()->id);
        
        return response()->json([
            'success' => $success,
            'unread_count' => $this->notificationService->getUnreadCount($request->user()->id)
        ]);
    }

    /**
     * Marque toutes les notifications comme lues
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $updated = NotificationModel::where('user_id', $user->id)
            ->where('status', NotificationModel::STATUS_NON_LU)
            ->update([
                'status' => NotificationModel::STATUS_LU,
                'read_at' => now()
            ]);
            
        return response()->json([
            'success' => true,
            'marked_read' => $updated,
            'unread_count' => 0
        ]);
    }
    
    /**
     * Récupère le nombre de notifications non lues
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount(Request $request)
    {
        $count = $this->notificationService->getUnreadCount($request->user()->id);
        
        return response()->json(['count' => $count]);
    }

    /**
     * Envoie une notification de paiement effectué
     *
     * @param  \App\Models\Paiement  $paiement
     * @return \Illuminate\Http\JsonResponse
     */
    public function notifierPaiementEffectue(Paiement $paiement)
    {
        try {
            $this->notificationService->notifyPaiementEffectue($paiement);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification de paiement envoyée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Vérifie et envoie les notifications pour les paiements en retard
     * Cette méthode est conçue pour être appelée par une tâche planifiée
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifierRetardsPaiement()
    {
        try {
            $this->notificationService->checkRetardsPaiement();
            
            return response()->json([
                'success' => true,
                'message' => 'Vérification des retards de paiement terminée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification des retards: ' . $e->getMessage()
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
