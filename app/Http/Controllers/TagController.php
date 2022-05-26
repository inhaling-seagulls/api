<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;

/**
 * @group Tag Management
 * 
 * API's call for Tags
 */
class TagController extends Controller
{
    /**
     * Display a listing of tags.
     * 
     * @apiResourceCollection App\Http\Resources\TagResource
     * @apiResourceModel App\Models\Tag
     * 
     * @return ResourceCollection
     */
    public function index()
    {
        return TagResource::collection(Tag::all());
    }
}
