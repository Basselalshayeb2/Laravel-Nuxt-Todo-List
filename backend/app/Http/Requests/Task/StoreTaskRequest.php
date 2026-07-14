<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'user_id' => ['prohibited'],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public function bodyParameters(): array
    {
        return [
            'title' => ['description' => 'A concise task title between 3 and 255 characters.', 'example' => 'Write project documentation'],
            'description' => ['description' => 'Optional task context.', 'example' => 'Document setup and API behavior.'],
            'due_date' => ['description' => 'Optional deadline in YYYY-MM-DD format.', 'example' => '2026-07-20'],
            'status' => ['description' => 'Task status; defaults to pending.', 'example' => 'pending'],
        ];
    }
}
