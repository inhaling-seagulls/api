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
     * Store a newly created user in storage and return a newly created token.
     * 
     * 
     * @bodyParam name string required Name of the User. Example: John Doe
     * @bodyParam email string required Email of the User. Example: johndoe@gmail.com
     * @bodyParam password string Password of the User. Example: password123
     * 
     * @response 201 {
     *  "data": {
     *      "token" : "api-token"  
     *   }
     * }
     * 
     * @param  \App\Http\Requests\Request  $request
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
     * Create a new token in storage and returns it.
     * 
     * 
     * @bodyParam email string required Email of the User. Example: johndoe@gmail.com
     * @bodyParam password string Password of the User. Example: password123
     * 
     * @response 201 {
     *  "data": {
     *      "token" : "api-token"  
     *   }
     * }
     * 
     * @param  \App\Http\Requests\Request  $request
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
     * Remove all user's tokens from storage.
     *  
     * @response 204
     * 
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response('', 204);
    }

    /**
     * Display user info.
     * 
     * @apiResource App\Http\Resources\AuthResource
     * 
     * @apiResourceModel App\Models\User with=profile,profile.tags,profile.projects,profile.projects.tags
     * 
     * @return AuthResource
     */
    public function me()
    {
        $me = auth()->user();
        $me = $me->load(['profile', 'profile.tags', 'profile.projects', 'profile.projects.tags']);
        return new AuthResource($me);
    }
}
