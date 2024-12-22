<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function getLoggedInUserInfo()
    {
        $user = Auth::user()->load(['following', 'followers']); //se podria separar en otra api para no saturar la peticion

        return response()->json([
            'user' => $user,
        ]);
    }

    public function getUserInfo($userId)
    {
        $user = User::findOrFail($userId)->load(['following', 'followers']);

        return response()->json([
            'user' => $user,
        ]);
    }

}
