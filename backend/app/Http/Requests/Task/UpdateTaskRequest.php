<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'user_id' => ['prohibited'],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public function bodyParameters(): array
    {
        return [
            'title' => ['description' => 'Replacement title between 3 and 255 characters.', 'example' => 'Publish project documentation'],
            'description' => ['description' => 'Replacement context or null.', 'example' => 'Complete the final review.'],
            'due_date' => ['description' => 'Replacement YYYY-MM-DD deadline or null.', 'example' => '2026-07-21'],
            'status' => ['description' => 'Replacement status.', 'example' => 'in_progress'],
        ];
    }
}
