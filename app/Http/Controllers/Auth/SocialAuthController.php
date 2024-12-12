<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Auth\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            // Obtener los datos del usuario desde el proveedor
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Crear o encontrar el usuario en la base de datos
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName(),
                    'password' => bcrypt('default_password'), // Contraseña genérica no utilizada
                    'socialite' => true,
                    'profile_photo_path' => $socialUser->getAvatar() ?? 'default_avatar.jpg',
                ]
            );

            // Crear token de acceso
            $token = $user->createToken('API Token')->plainTextToken;

            // Respuesta JSON con los datos del usuario y el token
            return response()->json([
                'message' => 'Autenticación exitosa',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Algo salió mal: ' . $e->getMessage()], 500);
        }
    }
}
