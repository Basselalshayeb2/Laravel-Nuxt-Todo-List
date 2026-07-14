<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_own_tasks_and_admin_sees_all(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->admin()->create();
        Task::factory()->for($user)->create(['title' => 'Mine']);
        Task::factory()->for($other)->create(['title' => 'Theirs']);

        $this->actingAs($user)->getJson('/api/tasks')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Mine');
        $this->actingAs($admin)->getJson('/api/tasks')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_filter_search_sort_and_pagination_are_supported(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['title' => 'Alpha documentation', 'description' => null, 'status' => TaskStatus::Pending, 'due_date' => '2026-07-30']);
        Task::factory()->for($user)->create(['title' => 'Beta', 'description' => 'Documentation checklist', 'status' => TaskStatus::Completed, 'due_date' => null]);
        Task::factory()->for($user)->create(['title' => 'Gamma', 'status' => TaskStatus::Pending, 'due_date' => '2026-07-20']);

        $this->actingAs($user)->getJson('/api/tasks?status=pending&sort=due_date&direction=asc&per_page=1&page=1')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Gamma')->assertJsonPath('meta.total', 2);
        $this->getJson('/api/tasks?search=documentation&sort=title&direction=asc')
            ->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.0.title', 'Alpha documentation');
    }

    public function test_invalid_query_parameters_return_validation_errors(): void
    {
        $this->actingAs(User::factory()->create())->getJson('/api/tasks?sort=user_id&direction=sideways&per_page=101')
            ->assertUnprocessable()->assertJsonValidationErrors(['sort', 'direction', 'per_page']);
    }
}
