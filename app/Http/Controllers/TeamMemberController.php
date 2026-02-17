<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Team $team)
    {
        $this->authorize('manage', $team);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        if ($user->role === 'team_leader') {
            return response()->json([
                'message' => 'Cannot assign a team leader as a member'
            ], 422);
        }
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot assign a admin as a member'
            ], 422);
        }

        $user->team_id = $team->id;
        $user->save();
        $user->load('team');

        return response()->json([
            'message' => 'Member added successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team, User $user)
    {
        $this->authorize('manage', $team);

        if ($user->id === $team->team_leader_id) {
            return response()->json([
                'message' => 'Cannot remove team leader'
            ]);
        }

        if ($user->team_id !== $team->id) {
            return response()->json([
                'message' => 'User is not a member of this team'
            ], 400);
        }
        $user->team_id = null;
        $user->save();

        return response()->json([
            'message' => 'Member removed successfully',
            'data' => $user
        ]);
    }
}
