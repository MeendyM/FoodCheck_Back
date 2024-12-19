<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Notification;
use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public function follow(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . Auth::id(),
        ]);

        // Verificar si la relación ya existe
        $existingFollow = Follower::where('user_id', Auth::id())
                                 ->where('follower_id', $request->input('user_id'))
                                 ->exists();

        if ($existingFollow) {
            return response()->json(['message' => 'Ya sigues a este usuario'], 400);
        }

        if ($request->input('user_id') == Auth::id()) {
            return response()->json(['message' => 'No te puedes seguir a ti mismo'], 400);
        }

        $follower = Follower::create([
            'user_id' => Auth::id(),
            'follower_id' => $request->input('user_id'),
        ]);

        // Obtener el nombre de usuario para la notificación
        $followerUser = User::findOrFail($request->input('user_id'));

        Notification::create([
            'user_id' => $request->input('user_id'),
            'type' => 'new_follower',
            'data' => [
                'follower_id' => Auth::id(),
                'follower_name' => Auth::user()->name, // Incluir el nombre de quien sigue
            ],
        ]);

        return response()->json(['message' => 'Usuario seguido con éxito'], 201);
    }

    public function unfollow(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|different:' . Auth::id(),
        ]);

        $follower = Follower::where('user_id', Auth::id())
            ->where('follower_id', $request->input('user_id'))
            ->firstOrFail();

        $follower->delete();

        return response()->json(['message' => 'Usuario dejado de seguir con éxito'], 200);
    }
}
