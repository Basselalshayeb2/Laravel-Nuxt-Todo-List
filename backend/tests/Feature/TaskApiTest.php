<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_use_error_contract(): void
    {
        $expected = [
            'success' => false,
            'message' => 'Authentication is required.',
            'code' => 'UNAUTHENTICATED',
            'errors' => [],
        ];

        $this->getJson('/api/tasks')->assertUnauthorized()->assertExactJson($expected);
        $this->get('/api/tasks')->assertUnauthorized()->assertExactJson($expected);
    }

    public function test_cors_uses_an_explicit_frontend_allowlist(): void
    {
        $this->withHeader('Origin', 'http://localhost')->getJson('/api/tasks')
            ->assertUnauthorized()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost')
            ->assertHeader('Access-Control-Allow-Credentials', 'true');

        $this->withHeader('Origin', 'https://untrusted.example')->getJson('/api/tasks')
            ->assertUnauthorized()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_user_can_create_view_update_and_delete_own_task(): void
    {
        $user = User::factory()->create();

        $create = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Write project notes',
            'description' => 'Capture the important decisions.',
            'due_date' => '2026-07-20',
        ])->assertCreated()->assertJsonPath('data.status', 'pending')->assertJsonPath('data.can.update', true);

        $taskId = $create->json('data.id');
        $this->getJson("/api/tasks/{$taskId}")->assertOk()->assertJsonPath('data.title', 'Write project notes');
        $this->patchJson("/api/tasks/{$taskId}", ['status' => 'completed'])
            ->assertOk()->assertJsonPath('data.status', 'completed');
        $this->deleteJson("/api/tasks/{$taskId}")->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    public function test_task_input_is_validated_and_user_id_is_prohibited(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'x', 'status' => 'unknown', 'due_date' => 'tomorrow', 'user_id' => $other->id,
        ])->assertUnprocessable()->assertJsonValidationErrors(['title', 'status', 'due_date', 'user_id']);
    }

    public function test_other_user_is_forbidden_and_admin_is_allowed(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->for($owner)->create();

        $this->actingAs($other)->getJson("/api/tasks/{$task->id}")->assertForbidden()->assertJsonPath('code', 'FORBIDDEN');
        $this->patchJson("/api/tasks/{$task->id}", ['title' => 'Forbidden change'])->assertForbidden();
        $this->deleteJson("/api/tasks/{$task->id}")->assertForbidden();

        $this->actingAs($admin)->patchJson("/api/tasks/{$task->id}", ['title' => 'Admin change'])
            ->assertOk()->assertJsonPath('data.title', 'Admin change');
    }

    public function test_missing_task_returns_not_found_contract(): void
    {
        $this->actingAs(User::factory()->create())->getJson('/api/tasks/99999')
            ->assertNotFound()->assertJsonPath('code', 'NOT_FOUND')->assertJsonStructure(['errors']);
    }
}
