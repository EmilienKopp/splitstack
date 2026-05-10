<?php

namespace App\Http\Requests;

use App\Application\Shared\Contracts\HasValidatedData;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest implements HasValidatedData
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
