<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


/**
 * @group Authentication Management
 * 
 * API's call to manage authentication services.
 */
class AuthController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // We assure that request has a correct format
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

        return response(["data" => ["token" => $token]], 201);
    }

    /**
     * Store a newly created token in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // We assure that request has a correct format
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

        return response(["data" => ["token" => $token]], 201);
    }

    /**
     * Delete all current user token in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response('', 204);
    }

    /**
     * Get all user info (except password, token, timestamps).
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        $me = auth()->user()->with(['profile', 'profile.tags', 'profile.projects', 'profile.projects.tags'])->first();
        return new AuthResource($me);
    }
}
