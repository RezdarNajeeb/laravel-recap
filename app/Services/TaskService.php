<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function __construct(protected TaskRepository $tasks)
    {
        //
    }

    public function store(array $data, int $userId): Task
    {
        $data['user_id'] = $userId;
        return $this->tasks->create($data);
    }

    public function getAllForUser(int $userId): LengthAwarePaginator
    {
        return $this->tasks->getAllForUser($userId);
    }

    public function update(Task $task, array $data): Task
    {
        return $this->tasks->update($task, $data);
    }

    public function delete(Task $task): void
    {
        $this->tasks->delete($task);
    }
}
