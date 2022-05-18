<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with(['tags', 'profile']);

        $match = request()->exists('match');
        if ($match) {
            $tags = Profile::first()->tags()->get(['id']); // This is temporary : Later we should use the current user profile to fetch tags

            $projects = $projects->whereHas('tags', function ($query) use ($tags) {
                return $query->whereIn('id', $tags);
            });
        }

        return ProjectResource::collection($projects->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectRequest $request)
    {
        $profile = Profile::where('user_id', '=', Auth::id())->first('id');

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
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project = $project->load(['tags', 'profile']);
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        if ($project->profile()->first()->user_id !== Auth::id()) {
            return response('', 401);
        }

        $project->delete();

        return response('', 204);
    }
}
