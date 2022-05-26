<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Resources\ProjectResource;

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

            // We fetch all tag ids of the current user
            $tags = auth()->user()->profile()->first()->tags()->get(['id']);

            // This filters out all projects that don't share at least one tag with the user
            $projects = $projects->whereHas('tags', function ($query) use ($tags) {
                return $query->whereIn('id', $tags);
            });
        }

        return ProjectResource::collection($projects->paginate(3));
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
