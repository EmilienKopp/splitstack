<?php

namespace App\Http\Requests\TimeTracking;

use App\Http\Requests\BaseRequest;

class ClockEntryStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:tenant.users,id'],
            'project_id' => ['required', 'integer', 'exists:tenant.projects,id'],
            'timezone' => ['required', 'string'],
            'in' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'out' => ['nullable', 'date_format:Y-m-d H:i:s', 'after:in'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->getKey(),
            'timezone' => $this->input('timezone') ?? $this->user()->timezone ?? config('app.timezone'),
        ]);
    }
}
