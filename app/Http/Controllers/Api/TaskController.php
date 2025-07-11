<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(protected TaskService $taskService)
    {
    }

    /**
     * Display a listing of the authenticated user’s tasks.
     */
    public function index(Request $request)
    {
        $tasks = $this->taskService->getAllForUser($request->user()->id);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->store($request->validated(), $request->user()->id);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $task = $this->taskService->getByIdForUser($id, $request->user()->id);

        if (!$task) {
            return $this->error('Task not found', 404);
        }

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, int $id)
    {
        try {
            $task = $this->taskService->getByIdForUser($id, $request->user()->id);

            if (!$task) {
                return $this->error('Task not found', 404);
            }

            $updatedTask = $this->taskService->update($task, $request->validated(), $request->user()->id);

            return new TaskResource($updatedTask);
        } catch (ModelNotFoundException $e) {
            return $this->error('Task not found or unauthorized', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $this->taskService->deleteByIdForUser($id, $request->user()->id);
            return $this->success('Task deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->error('Task not found or unauthorized', 404);
        }
    }
}
