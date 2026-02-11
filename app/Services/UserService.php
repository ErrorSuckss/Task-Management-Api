<?php

namespace App\Services;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserService
{
    /**
     * Create a new class instance.
     */

    protected User $user;
    public function __construct()
    {
        //
        $this->user = Auth::user();
    }

    public function getAccessibleUsers()
    {

        if ($this->user->isAdmin()) {
            return User::all();
        }

        if ($this->user->isTeamLeader()) {
            return User::where('team_leader_id', $this->user->id)->get();
        }

        return collect([$this->user]);
    }

    public function addUser(array $data)
    {

        if ($this->user->isAdmin()) {
            $data['role'] = $data['role'] ?? 'user';
            $user = User::create($data);
        } elseif ($this->user->isTeamLeader()) {
            $data['team_leader_id'] = $this->user->id;
            $data['role'] = 'user';
            $user = User::create($data);
            $user->load('teamLeader');
        } else {
            throw new \Exception("Unauthorized");
        }

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        if ($this->user->isAdmin()) {
            if (isset($data['role']) && !in_array($data['role'], ['admin', 'team_leader', 'user'])) {
                throw new \Exception('Invalid role.');
            }


            $user->update($data);
        } elseif ($this->user->isTeamLeader()) {
            if ($user->role !== 'user') {
                throw new \Exception('Team Leader can only update users.');
            }

            if (!$this->user->teamMembers()->where('id', $user->id)->exists()) {
                throw new \Exception('You can only update your own team members.');
            }

            unset($data['role']);

            $user->update($data);
        } else {
            if ($this->user->id !== $user->id) {
                throw new \Exception('You can only update your own account.');
            }

            unset($data['role'], $data['team_leader_id']);

            $user->update($data);
            $user->load('teamLeader');
        }

        return $user;
    }
}
