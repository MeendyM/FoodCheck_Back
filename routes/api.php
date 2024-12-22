<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');;
Route::post('/email/resend-verification', [VerificationController::class, 'resendVerificationEmail'])->middleware('throttle:6,1'); // Limita las solicitudes a 6 por minuto
Route::post('/login/token', [AuthController::class, 'loginWithToken']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('api.password.reset');
Route::get('/login/{provider}', [SocialAuthController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/follow', [FollowerController::class, 'follow']);
    Route::post('/unfollow', [FollowerController::class, 'unfollow']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/markAllAsRead', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/users/me', [UserController::class, 'getLoggedInUserInfo']);
    Route::get('/users/{userId}', [UserController::class, 'getUserInfo']);
});
