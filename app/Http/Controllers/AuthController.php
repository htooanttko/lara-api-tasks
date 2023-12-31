<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(! Auth::attempt($validated)){
            return response()->json([
                'message' => 'Login Information Invalid'
            ], 401);
        }

        $user = User::where('email' , $validated['email'])->first();
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:225',
            'email' => 'required|max:225|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create($validated);
        return response()->json([
            'data' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer'
        ], 201);
    }

    public function logout()
    {
       $user = Auth::user();
       $user->currentAccessToken()->delete();
       return response()->json([
        "message" => "Uer logout successfully!"
       ]);
    }
}
