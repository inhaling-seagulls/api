<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $projects = ProjectResource::collection(Project::with(['tags', 'profile'])->paginate());
        return response()->json($projects);
    }

    /**
     * Display a listing of the matching resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function match(Request $request)
    {
        $projects =
            ProjectResource::collection(
                Project::with(['tags'])->whereHas('tags', function ($query) use ($request) {
                    return $query->whereIn('tags.id', $request->tags);
                })->get()
            );

        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $project = new Project();
        $project->create($request->all())->tags()->attach($request->tags);

        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $project = new ProjectResource(Project::with(['tags', 'profile'])->findOrFail($id));

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $project->update($request->all());
        $project->tags()->sync($request->tags);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json();
    }
}
