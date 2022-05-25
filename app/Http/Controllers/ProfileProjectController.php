<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function index(Profile $profile)
    {
        $projects = $profile->projects()->with('tags')->get();
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Profile  $profile
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Profile $profile, Request $request)
    {
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
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile, $id)
    {
        $project = $profile->projects()->with('tags')->find($id);
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile, $id)
    {
        $project = $profile->projects()->find($id);
        $project->update($request->all());
        $project->tags()->sync($request->tags);
        $project = $project->load(['tags', 'profile']);

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile, $id)
    {
        $profile->projects()->find($id)->delete();

        return response('', 204);
    }
}
