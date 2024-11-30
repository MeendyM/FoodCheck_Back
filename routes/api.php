<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
Route::post('/login/facebook', [AuthController::class, 'loginWithFacebook']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1']) // Elimina 'auth'
    ->name('verification.verify');


    ;Route::post('/email/resend-verification', [VerificationController::class, 'resendVerificationEmail'])
    ->middleware('throttle:6,1'); // Limita las solicitudes a 6 por minuto
