<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userTaskId(Request $request)
    {
        if ($request->query('include') === 'user') {
            $tasks = Task::with('user')->get();
            return TaskResource::collection($tasks);
        }

        return response()->json(Task::all());
    }
    public function index()
    {
        $tasks = Task::all();
        return TaskResource::collection($tasks);
    }

    // public function user($id)
    // {
    //     $task = Task::findOrFail($id);
    //     return TaskResource::collection($task);
    // }

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
