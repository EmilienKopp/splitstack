<?php

namespace App\Casts;

use App\DTOs\N8nConfig;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class N8nConfigCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?N8nConfig
    {
        if (empty($value)) {
            return null;
        }

        // Handle if value is already an array (from JSONB)
        if (is_array($value)) {
            return N8nConfig::fromArray($value);
        }

        // Handle if value is a JSON string
        if (is_string($value)) {
            return N8nConfig::fromJson($value);
        }

        return null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof N8nConfig) {
            return $value->toJson();
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_string($value)) {
            // Validate it's valid JSON
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        throw new \InvalidArgumentException(
            'The given value must be an N8nConfig instance, array, or valid JSON string.'
        );
    }
}
