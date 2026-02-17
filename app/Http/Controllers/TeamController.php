<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authUser = $request->user();
        $query = Team::with('leader')->visibleTo($authUser);
        if ($request->query('include') === 'users') {
            $query->load('members');
        }
        return TeamResource::collection($query->get());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        //

        $validated = $request->validated();
        $leader = User::findOrFail($validated['team_leader_id']);

        $team = Team::create($validated);
        $leader->team_id = $team->id;
        $leader->save();
        $team->load('leader');

        return response()->json([
            'message' => 'Team created successfully.',
            'data' => new TeamResource($team)
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        //
        $validated = $request->validated();
        $team->update($validated);

        return response()->json([
            'message' => 'Team updated successfully.',
            'data' => new TeamResource($team)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully.',
            'data' => new TeamResource($team)
        ]);
    }
}
