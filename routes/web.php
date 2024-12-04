<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/correo-verificado', function () {
    return view('email-verified');
});
Route::get('/password/reset/{token}', function (Request $request, string $token) {
    return view('auth.reset-password', ['request' => $request, 'token' => $token]);
})->middleware('guest')->name('password.reset');
