<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; // Importa el modelo User
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified; // Importa la clase Verified
use App\Http\Requests\VerifyEmailRequest; // Importa la nueva clase
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

class VerificationController
{
    public function verify(VerifyEmailRequest $request) // Usa la nueva clase aquí
    {
        // Obtén al usuario a partir del ID en la URL
        $user = User::findOrFail($request->route('id'));

        // Verifica si el usuario ya ha verificado su correo electrónico
        if ($user->hasVerifiedEmail()) {
            return redirect('/correo-verificado');
        }

        // Marca al usuario como verificado
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('/correo-verificado');
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
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Ya has iniciado sesión y tu correo electrónico está verificado.'], 400);
        }

        // 4. Reenviar el correo de verificación
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado.'], 200);
    }
}



//opcion 1, dejar asi el back y en el front actualizar cada 5 segundos para verificar si el usario ya está atenticado (email_verified_at)para que se inicie sesión automaticamente
/*
Angular
if (response.email_verified_at) {
            // El usuario ha sido verificado, iniciar sesión automáticamente
            this.http.post('/api/login', { email: response.email, password: 'user_password' }) // Reemplazar 'user_password' con la contraseña del usuario si es necesario
              .subscribe((loginResponse: any) => {
                // Guardar el token de acceso en el almacenamiento local o en una cookie
                localStorage.setItem('token', loginResponse.token);
                // Redirigir al usuario a la página principal
                // ...
              });
          }
*/

//opcion 2, modificarlo y cuando se de click en el boton de verificar lo reedija al al front con los datos para iniciar sesion
