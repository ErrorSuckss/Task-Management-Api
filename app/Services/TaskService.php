<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected User $user;
    public function __construct()
    {
        //
        $this->user = Auth::user();
    }

    public function getAccessibleTasks()
    {
        if ($this->user->isAdmin()) {
            return Task::all();
        } elseif ($this->user->isTeamLeader()) {
            return Task::whereHas('user', function ($query) {
                $query->where('team_leader_id', $this->user->id);
            })->get();
        } else {
            return $this->user->tasks()->get();
        }
    }
}
