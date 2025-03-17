<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try {
            return response()->json(User::all(), 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Error fetching users',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    
        public function login(Request $request)
        {
            try {
                if (Auth::attempt([
                    'email' => $request->email,
                    'password' => $request->password,
                ])) {
                    $user = Auth::user();
                    $token = $user->createToken('myToken')->plainTextToken;
                    return response()->json([
                        'user' => $user,
                        'token' => $token,
                    ], 200);
                }
                return response()->json([
                    'message' => 'Invalid Credentials',
                ], 401);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'An error occurred during login',
                    'error' => $e->getMessage(),
                ], 500);
        }
    }
}
