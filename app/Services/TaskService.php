<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    public function __construct(protected TaskRepository $tasks)
    {
    }

    public function store(array $data, int $userId): Task
    {
        $data['user_id'] = $userId;
        return $this->tasks->create($data);
    }

    public function getByIdForUser(int $id, int $userId): ?Task
    {
        return $this->tasks->findByIdAndUser($id, $userId);
    }

    public function getAllForUser(int $userId): array
    {
        return $this->tasks->getAllForUser($userId);
    }

    public function update(Task $task, array $data, int $userId): Task
    {
        if ($task->user_id !== $userId) {
            throw new ModelNotFoundException('Task not found or unauthorized.');
        }

        return $this->tasks->update($task, $data);
    }

    public function deleteByIdForUser(int $id, int $userId): void
    {
        $task = $this->getByIdForUser($id, $userId);
        if (!$task) {
            throw new ModelNotFoundException('Task not found or unauthorized.');
        }
        $this->tasks->delete($task);
    }
}
