<?php

namespace App\Http\Requests\TimeTracking;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Requests\BaseRequest;

class CreateProjectRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'organization_id' => ['nullable', 'integer'],
            'type' => ['required', 'string', 'in:'.implode(',', ProjectType::values())],
            'status' => ['required', 'string', 'in:'.implode(',', ProjectStatus::values())],
            'start_date' => ['nullable', 'date'],
            'user_id' => ['required', 'integer'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->getKey(),
            'type' => $this->input('type') ?? ProjectType::Other->value,
            'status' => $this->input('status') ?? ProjectStatus::Active->value,
        ]);
    }
}
