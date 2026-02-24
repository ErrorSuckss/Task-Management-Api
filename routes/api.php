<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return new UserResource($request->user());
    });

    // Route::get('/users/task', [UserController::class, 'arrayOfUsersWithTasks']);
    // Route::get('/users/{user}/user', [UserController::class, 'tasks']);

    // // Route::get('/tasks', [TaskController::class, 'index']);
    // Route::get('/tasks/user', [TaskController::class, 'arrayOfTasksWithUser']);
    // Route::get('/tasks/{task}/user', [TaskController::class, 'user']);



    Route::middleware('checkRole:admin')->group(function () {
        Route::post('/teams', [TeamController::class, 'store']);
        Route::put('/teams/{team}', [TeamController::class, 'update']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::get('export/teams', [TeamController::class, 'export']);
        Route::post('import/teams', [TeamController::class, 'import']);
    });

    Route::middleware('checkRole:team_leader,admin')->group(function () {
        Route::post('/teams/{team}/members', [TeamMemberController::class, 'store']);
        Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy']);
    });

    Route::get('/teams', [TeamController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);


    Route::apiResource('tasks', TaskController::class);
});
