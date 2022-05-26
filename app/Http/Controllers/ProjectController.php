<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Resources\ProjectResource;

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
}
