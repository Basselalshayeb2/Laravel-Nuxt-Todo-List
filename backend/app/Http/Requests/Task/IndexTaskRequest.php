<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['due_date', 'status', 'title', 'created_at', 'updated_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public function queryParameters(): array
    {
        return [
            'status' => ['description' => 'Filter by pending, in_progress, or completed.', 'example' => 'pending'],
            'search' => ['description' => 'Search task titles and descriptions.', 'example' => 'documentation'],
            'sort' => ['description' => 'Sort by due_date, status, title, created_at, or updated_at.', 'example' => 'due_date'],
            'direction' => ['description' => 'Sort direction: asc or desc.', 'example' => 'asc'],
            'page' => ['description' => 'One-based page number.', 'example' => 1],
            'per_page' => ['description' => 'Items per page, from 1 to 100.', 'example' => 10],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $this->merge(['search' => trim((string) $this->input('search'))]);
        }
    }
}
