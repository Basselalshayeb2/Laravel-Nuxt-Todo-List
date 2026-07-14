<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Demo Admin',
            'password' => 'Password123!',
            'role' => UserRole::Admin,
        ]);

        $user = User::query()->updateOrCreate(['email' => 'user@example.com'], [
            'name' => 'Demo User',
            'password' => 'Password123!',
            'role' => UserRole::User,
        ]);

        $this->seedTask($admin, 'Review team priorities', TaskStatus::InProgress, now()->addDays(4)->toDateString());
        $this->seedTask($user, 'Plan the week', TaskStatus::Pending, now()->addDays(2)->toDateString());
        $this->seedTask($user, 'Archive completed notes', TaskStatus::Completed, null);
    }

    private function seedTask(User $user, string $title, TaskStatus $status, ?string $dueDate): void
    {
        Task::query()->updateOrCreate(
            ['user_id' => $user->id, 'title' => $title],
            ['description' => 'Demo task created by the database seeder.', 'status' => $status, 'due_date' => $dueDate],
        );
    }
}
