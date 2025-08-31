<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Récupère les statistiques des notifications pour l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistiques()
    {
        $user = Auth::user();
        
        return response()->json([
            'total_notifications' => $user->notifications()->count(),
            'notifications_lues' => $user->readNotifications()->count(),
            'notifications_non_lues' => $user->unreadNotifications()->count(),
            'notifications_aujourd_hui' => $user->notifications()
                ->whereDate('created_at', today())
                ->count(),
            'notifications_cette_semaine' => $user->notifications()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ]);
    }
}
