<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $loginToken = Str::random(60);
        // Guardar el token en la base de datos (ejemplo con tabla separada)
        DB::table('login_tokens')->insert([
            'user_id' => $user->id,
            'token' => $loginToken,
            'created_at' => now(),
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Usuario registrado. Por favor verifica tu correo.',
            'login_token' => $loginToken
        ], 201);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = $request->user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Por favor verifica tu correo electrónico.'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    public function loginWithToken(Request $request)
    {
        // Validar el input
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        try {
            // Buscar el token en la base de datos
            $loginToken = DB::table('login_tokens')->where('token', $validated['token'])->first();

            if (!$loginToken) {
                return response()->json([
                    'error' => 'Token inválido o expirado.',
                    'hint' => 'Verifica que el token sea correcto o solicita uno nuevo.',
                ], 401);
            }

            // Obtener el usuario relacionado al token
            $user = User::find($loginToken->user_id);

            if (!$user) {
                return response()->json([
                    'error' => 'Usuario no encontrado.',
                    'hint' => 'Es posible que el usuario relacionado al token ya no exista.',
                ], 404);
            }

            // Generar un token Sanctum
            $sanctumToken = $user->createToken('authToken')->plainTextToken;

            // Eliminar el token de inicio de sesión de la base de datos
            DB::table('login_tokens')->where('token', $validated['token'])->delete();

            // Retornar el token Sanctum y la información del usuario
            return response()->json([
                'message' => 'Inicio de sesión exitoso.',
                'token' => $sanctumToken,
                'user' => $user,
            ], 200);

        } catch (\Exception $e) {
            // Manejo general de errores
            return response()->json([
                'error' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
