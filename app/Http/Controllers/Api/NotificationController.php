<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\NotificationService  $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth:api');
        $this->notificationService = $notificationService;
    }

    /**
     * Get the authenticated user's notifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->input('limit', 15);
        $unreadOnly = $request->boolean('unread_only', false);
        
        $query = $user->notifications();
        
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }
        
        $notifications = $query->latest()
            ->paginate($limit);
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'La notification était déjà marquée comme lue.',
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.',
            'unread_count' => 0,
        ]);
    }

    /**
     * Get the notification preferences for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? config('notifications.default_preferences', []);
        
        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Update the notification preferences for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $preferences = $request->validate([
            'email' => 'sometimes|boolean',
            'database' => 'sometimes|boolean',
            'push' => 'sometimes|boolean',
            'sms' => 'sometimes|boolean',
            'types' => 'sometimes|array',
            'types.*' => 'boolean',
        ]);
        
        $user->notification_preferences = array_merge(
            $user->notification_preferences ?? [],
            $preferences
        );
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Préférences de notification mises à jour avec succès.',
            'data' => $user->notification_preferences,
        ]);
    }

    /**
     * Get the count of unread notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Get a specific notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Marquer comme lue lors de la consultation
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
        
        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }

    /**
     * Delete a notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée avec succès.',
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Clear all notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearAll()
    {
        $user = Auth::user();
        $user->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été supprimées.',
            'unread_count' => 0,
        ]);
    }
}
