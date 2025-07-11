<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(protected TaskService $taskService)
    {
    }

    public function index(Request $request)
    {
        $tasks = $this->taskService->getAllForUser($request->user()->id);
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->store($request->validated(), $request->user()->id);
        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $updatedTask = $this->taskService->update($task, $request->validated());
        return new TaskResource($updatedTask);
    }

    public function destroy(Task $task)
    {
        $this->taskService->delete($task);
        return $this->success('Task deleted successfully');
    }
}
