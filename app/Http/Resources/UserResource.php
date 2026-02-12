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
            'email' => $this->email,
            'role' => $this->role,
            'team_leader_id' => $this->team_leader_id,
            'team_leader' => new UserResource($this->whenLoaded('teamLeader')),
            'team_members' => UserResource::collection($this->whenLoaded('teamMembers')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks'))
        ];
    }
}
