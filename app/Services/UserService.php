<?php

namespace App\Services;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Create a new class instance.
     */



    public function getAccessibleUsers(User $authUser)
    {

        if ($authUser->isAdmin()) {
            return User::all();
        }

        if ($authUser->isTeamLeader()) {
            return User::where('team_leader_id', $authUser->id)->get();
        }

        return collect([$authUser]);
    }

    public function addUser(User $authUser, array $data)
    {

        if ($authUser->isAdmin()) {
            $data['role'] = $data['role'] ?? 'user';
            $user = User::create($data);
        } elseif ($authUser->isTeamLeader()) {
            $data['team_leader_id'] = $authUser->id;
            $data['role'] = 'user';
            $user = User::create($data);
            $user->load('teamLeader');
        } else {
            throw new \Exception("Unauthorized");
        }

        return $user;
    }

    public function updateUser(User $authUser, User $user, array $data, $request)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }


        if ($authUser->isAdmin()) {
            if (isset($data['role']) && !in_array($data['role'], ['admin', 'team_leader', 'user'])) {
                throw new \Exception('Invalid role.');
            }
            if (isset($data['role']) && $data['role'] === 'team_leader') {
                $data["team_leader_id"] = null;
            }
            if ($request->hasFile('profile_pic')) {
                $user = $this->updateProfilePic($user, $user->file['profile_pic']);
                $data['profile_pic'] = $user->profile_pic;
            }

            $user->update($data);
        } elseif ($authUser->isTeamLeader()) {
            if ($user->role !== 'user') {
                throw new \Exception('Team Leader can only update users.');
            }

            if (!$authUser->teamMembers()->where('id', $user->id)->exists()) {
                throw new \Exception('You can only update your own team members.');
            }

            if ($request->hasFile('profile_pic')) {
                $user = $this->updateProfilePic($user, $user->file['profile_pic']);
                $data['profile_pic'] = $user->profile_pic;
            }

            unset($data['role'], $data['team_leader_id']);

            $user->update($data);
        } else {
            if ($authUser->id !== $user->id) {
                throw new \Exception('You can only update your own account.');
            }
            if ($request->hasFile('profile_pic')) {
                $user = $this->updateProfilePic($user, $request->file('profile_pic'));
                $data['profile_pic'] = $user->profile_pic;
            }

            unset($data['role'], $data['team_leader_id']);

            $user->update($data);
        }

        if ($user->team_leader_id) {
            $user->load('teamLeader');
        }
        if ($user->role == 'team_leader') {
            $user->load('teamMembers');
        }

        return $user;
    }


    public function updateProfilePic(User $user, $uploadedFile)
    {
        if (!$uploadedFile) {
            return $user;
        }

        if ($user->profile_pic) {
            Storage::disk('public')->delete($user->profile_pic);
        }


        $path = $uploadedFile->store('users', 'public');
        $user->profile_pic = $path;
        $user->save();
        return $user;
    }
}
