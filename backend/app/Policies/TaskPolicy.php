<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

final class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->isAdmin() || $user->id === $task->user_id;
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }
}
