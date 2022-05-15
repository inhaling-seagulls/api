<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'pseudo' => $this->pseudo,
                'contact' => $this->contact,
                'projects' => ProjectResource::collection($this->whenLoaded('projects')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ];
    }
}
