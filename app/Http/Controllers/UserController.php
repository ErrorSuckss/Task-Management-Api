<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function arrayOfUsersWithTasks(Request $request)
    {
        if ($request->query('include') === 'tasks') {
            $users = User::with('tasks')->get();
            return UserResource::collection($users);
        }

        return response()->json(["message" => "No Tasks included."]);
    }

    public function tasks(User $user)
    {
        return response()->json($user->tasks);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserService $userService)
    {

        return UserResource::collection($userService->getAccessibleUsers());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request, UserService $userService)
    {
        $validated = $request->validated();
        $user = $userService->addUser($validated);
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UserService $userService)
    {
        //
        $validated = $request->validated();
        $user = $userService->updateUser($user, $validated);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
