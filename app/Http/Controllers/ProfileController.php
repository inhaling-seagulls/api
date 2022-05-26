<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

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
     * @apiResource App\Http\Resources\ProfileResource
     * @apiResourceModel App\Models\Profile with=projects,tags
     * 
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return ProfileResource
     */
    public function store(StoreProfileRequest $request)
    {
        $profile = Profile::create([
            'pseudo' => $request->input('pseudo'),
            'contact' => $request->input('contact'),
            'user_id' => Auth::id()
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
     * @apiResource App\Http\Resources\ProfileResource
     * @apiResourceModel App\Models\Profile with=projects,tags
     * 
     * @param  \App\Http\Requests\UpdateProfileRequest  $request
     * @return ProfileResource
     */
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
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
        // We compare profile.user_id and authoticated user's id before deleting  
        if ($profile->user_id !== Auth::id()) {
            return response('', 401);
        }

        $profile->delete();

        return response('', 204);
    }
}
