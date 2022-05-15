<?php

namespace App\Http\Requests;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        if (!$profile) return false;

        $project = $this->route('project.id');

        return Project::where('id', $project)->where('profile_id', $profile['id'])->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
