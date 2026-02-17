<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'team_id' => $this->team_id,
            'email' => $this->email,
            'role' => $this->role,
            'team' => new TeamResource($this->whenLoaded('team')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks'))
        ];
    }
}
