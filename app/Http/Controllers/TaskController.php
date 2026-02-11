<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function arrayOfTasksWithUser(Request $request)
    {
        if ($request->query('include') === 'user') {
            $tasks = Task::with('user')->get();
            return TaskResource::collection($tasks);
        }

        return response()->json(Task::all());
    }
    public function index(TaskService $taskService)
    {
        return TaskResource::collection($taskService->getAccessibleTasks());
    }

    public function user(Task $task)
    {
        $task->load('user');

        if (!$task->user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserResource($task->user);
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
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        //
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Not allowed'
            ]);
        }
        $validated = $request->validated();
        $task->update($validated);

        return response()->json([
            'message' => 'Task Updated Successfully',
            'data' => new TaskResource($task)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
