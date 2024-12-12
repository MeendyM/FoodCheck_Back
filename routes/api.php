<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1']) ->name('verification.verify');;
Route::post('/email/resend-verification', [VerificationController::class, 'resendVerificationEmail'])->middleware('throttle:6,1'); // Limita las solicitudes a 6 por minuto
Route::post('/login/token', [AuthController::class, 'loginWithToken']);

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('api.password.reset');

Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
