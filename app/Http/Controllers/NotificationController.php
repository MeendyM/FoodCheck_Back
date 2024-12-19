<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para marcar esta notificación como leída.');
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notificación marcada como leída'], 200);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas'], 200);
    }
}
