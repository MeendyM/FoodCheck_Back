<?php

namespace App\Http\Controllers;

use App\Models\User; // Importa el modelo User
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified; // Importa la clase Verified
use App\Http\Requests\VerifyEmailRequest; // Importa la nueva clase
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function verify(VerifyEmailRequest $request) // Usa la nueva clase aquí
    {
        // Obtén al usuario a partir del ID en la URL
        $user = User::findOrFail($request->route('id'));

        // Verifica si el usuario ya ha verificado su correo electrónico
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'El correo electrónico ya ha sido verificado'], 400);
        }

        // Marca al usuario como verificado
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Correo electrónico verificado con éxito']);
    }

    public function resendVerificationEmail(Request $request)
    {
        // 1. Validar la solicitud
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Obtener el usuario
        $user = User::where('email', $request->email)->first();

        // 3. Verificar si el usuario está autenticado
        if (Auth::user() && Auth::user()->id === $user->id && $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Ya has iniciado sesión y tu correo electrónico está verificado.'], 400);
        }

        // 4. Reenviar el correo de verificación
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado.'], 200);
    }
}



