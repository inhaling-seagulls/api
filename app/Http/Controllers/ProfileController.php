<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProfileResource::collection(Profile::with(['tags', 'projects'])->paginate(3));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request;  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // We check if a profile already exists for this user
        if (auth()->user()->profile()->exists()) return response(["message" => "You can't create multiple profiles"], 409);

        $profile = Profile::create([
            'pseudo' => $request->input('pseudo'),
            'contact' => $request->input('contact'),
            'user_id' => auth()->id()
        ]);
        $profile->tags()->sync($request->tags);
        $profile = $profile->load('tags');

        return new ProfileResource($profile);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        $profile = $profile->load(['tags', 'projects']);
        return new ProfileResource($profile);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Illuminate\Http\Request;  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        // Just in case, we check if this is the right person who tries to update
        if ($profile->user_id !== auth()->id()) return response(["message" => "This resource belongs to another account"], 403);

        $profile->update($request->all());
        $profile->tags()->sync($request->tags);
        $profile = $profile->load(['tags', 'projects']);

        return new ProfileResource($profile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        // Just in case, we check if this is the right person who tries to delete
        if ($profile->user_id !== auth()->id()) return response(["message" => "This resource belongs to another account"], 403);

        $profile->delete();

        return response('', 204);
    }
}
