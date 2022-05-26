<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Profile;
use Illuminate\Http\Request;

/**
 * @group Project Management
 * 
 * API's call for project resources.
 */
class ProfileProjectController extends Controller
{
    /**
     * Display a listing of projects belonging to specified profile.
     * 
     * @urlParam profile int required Profile ID
     * 
     * @queryParam page int Page to view. Example: 1
     * @queryParam match bool Matching projects with profile preferences. Example: 0
     * 
     * @apiResourceCollection App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     * 
     * @return ResourceCollection
     */
    public function index(Profile $profile)
    {
        $projects = $profile->projects()->with('tags')->paginate(2);
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project in storage.
     * 
     * @urlParam profile int required Profile ID
     * 
     * @bodyParam name string required Name of the project. Example: Project 1
     * @bodyParam description string required Description of the project. Example: This project is very cool
     * @bodyParam image string Image of the project. Example: https://images.pexels.com/photos/4678283/pexels-photo-4678283.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1
     * @bodyParam tags array List of tag id the project. Example: [1, 2]
     *
     * @apiResourceCollection App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     * 
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return ProjectResource
     */
    public function store(Profile $profile, Request $request)
    {
        // Just in case, we check if this is the right person who tries to store
        if ($profile->user_id !== auth()->id()) return response(["message" => "This resource belongs to another account"], 403);

        $project = $profile->projects()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $request->input('image'),
            'profile_id' => $profile->id
        ]);
        $project->tags()->sync($request->tags);
        $project = $project->load(['tags', 'profile']);

        return new ProjectResource($project);
    }

    /**
     * Display the specified project belonging to specified profile.
     *
     * @urlParam profile int required Profile ID
     * @urlParam id int required Project ID
     * 
     * @apiResource App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     * 
     * @param  \App\Models\Project  $project
     * @return ProjectResource
     */
    public function show(Profile $profile, $id)
    {
        $project = $profile->projects()->with('tags')->find($id);
        return new ProjectResource($project);
    }

    /**
     * Update the specified project in storage.
     * 
     * @urlParam profile int required Profile ID
     * @urlParam id int required Project ID
     * 
     * @bodyParam name string required Name of the project. Example: Project 1
     * @bodyParam description string required Description of the project. Example: This project is very cool
     * @bodyParam image string Image of the project. Example: https://images.pexels.com/photos/4678283/pexels-photo-4678283.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1
     * @bodyParam tags array List of tag id the project. Example: [1, 2]
     * 
     * @apiResource App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile, $id)
    {
        // Just in case, we check if this is the right person who tries to update
        if ($profile->user_id !== auth()->id()) return response(["message" => "This resource belongs to another account"], 403);

        $project = $profile->projects()->find($id);
        $project->update($request->all());
        $project->tags()->sync($request->tags);
        $project = $project->load(['tags', 'profile']);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified project from storage.
     *
     * @urlParam profile int required Profile ID
     *  
     * @response 204
     * 
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile, $id)
    {
        // Just in case, we check if this is the right person who tries to delete
        if ($profile->user_id !== auth()->id()) return response(["message" => "This resource belongs to another account"], 403);

        $profile->projects()->find($id)->delete();

        return response('', 204);
    }
}
