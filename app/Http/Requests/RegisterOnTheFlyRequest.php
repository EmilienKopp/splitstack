<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterOnTheFlyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'org_name' => ['required', 'string', 'max:255'],
            'org_slug' => ['required', 'string', 'max:255', 'unique:tenants,space'],
            'org_id' => ['required', 'string', 'max:255', 'unique:tenants,org_id'],
            'workos_id' => ['required', 'string', 'max:255', 'unique:users,workos_id'],
            'avatar' => ['required', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('name') ?? "{$this->input('first_name')} {$this->input('last_name')}",
        ]);
    }
}
