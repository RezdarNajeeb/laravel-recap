<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function findByIdAndUser(int $id, int $userId): ?Task
    {
        return Task::where('id', $id)->where('user_id', $userId)->first();
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function getAllForUser(int $userId): array
    {
        return Task::where('user_id', $userId)->get();
    }
}
