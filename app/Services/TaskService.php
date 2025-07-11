<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TaskService
{
    protected const CACHE_TTL = 300; // 5 minutes

    public function __construct(protected TaskRepository $tasks)
    {
        //
    }

    public function store(array $data, int $userId): Task
    {
        $data['user_id'] = $userId;
        $task = $this->tasks->create($data);

        $this->clearUserTasksCache($userId);

        return $task;
    }

    /**
     * Return all tasks for a user, cached.
     * @return Collection
     */
    public function getAllForUser(int $userId): Collection
    {
        $cacheKey = "user:{$userId}:tasks";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return $this->tasks->getAllForUser($userId);
        });
    }

    public function update(Task $task, array $data): Task
    {
        $updatedTask = $this->tasks->update($task, $data);

        $this->clearUserTasksCache($task->user_id);

        return $updatedTask;
    }

    public function delete(Task $task): void
    {
        $userId = $task->user_id;
        $this->tasks->delete($task);

        $this->clearUserTasksCache($userId);
    }

    private function clearUserTasksCache(int $userId): void
    {
        Cache::forget("user:{$userId}:tasks");
    }
}
