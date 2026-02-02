<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}/user', [UserController::class, 'tasks']);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}/user', [TaskController::class, 'user']);
});
