<?php

namespace App\Http\Controllers;

use App\Models\Project;
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

        // Filters project. We get only projects where project tags includes authenticated user tags
        $match = request()->exists('match');
        if ($match) {
            $tags = Profile::where('user_id', Auth::id())->first()->tags()->get(['id']);
            $projects = $projects->whereHas('tags', function ($query) use ($tags) {
                return $query->whereIn('id', $tags);
            });
        }

        return ProjectResource::collection($projects->paginate());
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
}
