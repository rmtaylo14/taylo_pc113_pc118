<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }
    public function login(Request $request)
    {
        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]))
        {
            $user = Auth::user();
            $token = $user->createToken('myToken')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }
        return response ()->json([
            'message' => 'Invalid Credentials', 
        ]);
    }
}
