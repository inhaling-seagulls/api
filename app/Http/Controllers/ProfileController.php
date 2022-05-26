<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;

/**
 * @group Profile Management
 * 
 * API's call for profile resources.
 */
class ProfileController extends Controller
{
    /**
     * Display a listing of profiles.
     * 
     * @queryParam page int Page to view. Example: 1
     * 
     * @apiResourceCollection App\Http\Resources\ProfileResource
     * @apiResourceModel App\Models\Profile with=projects,tags
     * 
     * @return ResourceCollection
     */
    public function index()
    {
        return ProfileResource::collection(Profile::with(['tags', 'projects'])->paginate(3));
    }

    /**
     * Store a newly created profile in storage.
     * 
     * @bodyParam pseudo string required Pseudo of the profile. Example: JohnDoe06
     * @bodyParam contact string required Contact of the profile. Example: Linkedin : JohnDoe06 Tel: 00 00 00 00 00
     * @bodyParam tags array List of tag id the profile. Example: [1, 2]
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
     * Display the specified profile.
     *
     * @urlParam id int required Profile ID
     * 
     * @apiResource App\Http\Resources\ProfileResource
     * @apiResourceModel App\Models\Profile with=projects,tags
     * 
     * @param  \App\Models\Profile  $profile
     * @return ProfileResource
     */
    public function show(Profile $profile)
    {
        $profile = $profile->load(['tags', 'projects']);
        return new ProfileResource($profile);
    }

    /**
     * Store a newly created profile in storage.
     * 
     * @urlParam id int required Profile ID
     * 
     * @bodyParam pseudo string required Pseudo of the profile. Example: JohnDoe06
     * @bodyParam contact string required Contact of the profile. Example: Linkedin : JohnDoe06 Tel: 00 00 00 00 00
     * @bodyParam tags array List of tag id the profile. Example: [1, 2]
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
     * Remove the specified profile from storage.
     *
     * @response 204
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
