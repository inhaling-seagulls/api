<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
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
        $user['token'] = $token;
        $user = $user->load('profile');
        $user = $user->load('profile.projects');

        return new AuthResource($user);
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
                'message' => 'Password or Email is incorrect'
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        // We format user so it includes token
        $user['token'] = $token;
        $user = $user->load(['profile', 'profile.tags', 'profile.projects', 'profile.projects.tags']);

        return new AuthResource($user);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response('', 204);
    }
}
