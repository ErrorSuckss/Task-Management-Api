<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTeamLeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $team = $request->route('team');
        $user = $request->user();

        if (!$team) {
            return response()->json([
                'message' => 'Team not found.'
            ], 404);
        }

        if ($user->role !== 'admin' && $team->team_leader_id !== $user->id) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }


        return $next($request);
    }
}
