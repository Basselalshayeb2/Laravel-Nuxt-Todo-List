<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TaskController extends Controller
{
    /**
     * List tasks
     *
     * Users see their tasks; administrators see every task.
     *
     * @group Tasks
     */
    public function index(IndexTaskRequest $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();
        $sort = (string) ($validated['sort'] ?? 'created_at');
        $direction = (string) ($validated['direction'] ?? 'desc');

        $query = Task::query()
            ->when(! $user->isAdmin(), fn (Builder $query): Builder => $query->where('user_id', $user->id))
            ->when($validated['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->when($validated['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        if ($sort === 'due_date') {
            $query->orderByRaw('due_date IS NULL')->orderBy('due_date', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $tasks = $query->orderBy('id', $direction)
            ->paginate((int) ($validated['per_page'] ?? 10))
            ->withQueryString();

        return TaskResource::collection($tasks)->additional(['success' => true]);
    }

    /**
     * Create a task
     *
     * @group Tasks
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $task = $user->tasks()->create($request->validated())->refresh();

        return (new TaskResource($task))
            ->additional(['success' => true])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a task
     *
     * @group Tasks
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return (new TaskResource($task))->additional(['success' => true]);
    }

    /**
     * Update a task
     *
     * @group Tasks
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);
        $task->update($request->validated());

        return (new TaskResource($task->refresh()))->additional(['success' => true]);
    }

    /**
     * Delete a task
     *
     * @group Tasks
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return ApiResponse::success(null, 'Task deleted successfully.');
    }
}
