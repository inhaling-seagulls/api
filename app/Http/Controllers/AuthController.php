<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        // We format user so it includes token
        $data = ['user' => $user];
        $data['user']['token'] = $token;

        return response([
            'data' => $data
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'data' => [
                    'message' => 'Password or Email is incorrect'
                ]
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        // We format user so it includes token
        $data = ['user' => $user];
        $data['user']['token'] = $token;

        return response([
            'data' => $data
        ], 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response('', 204);
    }
}
