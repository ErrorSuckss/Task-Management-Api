<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamResource extends JsonResource
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
            'profile_pic' => $this->profile_pic ? asset('storage/' . $this->profile_pic) : null,
            'team_leader_id' => $this->team_leader_id,
            'leader' => new UserResource($this->whenLoaded('leader')),
            'members' => TeamResource::collection($this->whenLoaded('members'))
        ];
    }
}
