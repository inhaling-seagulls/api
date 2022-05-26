<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

/**
 * @group Project Management
 * 
 * API's call for project resources.
 */
class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     * 
     * @queryParam page int Page to view. Example: 1
     * @queryParam match bool Matching projects with profile preferences. Example: 0
     * 
     * @apiResourceCollection App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     * 
     * @return ResourceCollection
     */
    public function index()
    {
        $projects = Project::with(['tags', 'profile']);

        // Filters project. We get only projects where project tags includes authenticated user tags
        $match = request('match');
        if ($match) {
            $tags = Profile::where('user_id', Auth::id())->first()->tags()->get(['id']);
            $projects = $projects->whereHas('tags', function ($query) use ($tags) {
                return $query->whereIn('id', $tags);
            });
        }

        return ProjectResource::collection($projects->paginate());
    }

    /**
     * Store a newly created project in storage.
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
    public function store(StoreProjectRequest $request)
    {
        $profile = Profile::where('user_id', Auth::id())->first('id');

        if (!$profile) return response(['message' => 'No profile found'], 404);

        $project = Project::create([
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
     * Display the specified project.
     *
     * @urlParam id int required Project ID
     * 
     * @apiResource App\Http\Resources\ProjectResource
     * @apiResourceModel App\Models\Project with=profile,tags
     * 
     * @param  \App\Models\Project  $project
     * @return ProjectResource
     */
    public function show(Project $project)
    {
        $project = $project->load(['tags', 'profile']);
        return new ProjectResource($project);
    }

    /**
     * Update the specified project in storage.
     * 
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
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->all());
        $project->tags()->sync($request->tags);
        $project = $project->load(['tags', 'profile']);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified project from storage.
     *
     * @response 204
     * 
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // We compare profile.user_id and authoticated user's id before deleting 
        if ($project->profile()->first()->user_id !== Auth::id()) {
            return response('', 401);
        }

        $project->delete();

        return response('', 204);
    }
}
