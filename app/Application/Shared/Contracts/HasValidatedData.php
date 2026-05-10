<?php

namespace App\Application\Shared\Contracts;

interface HasValidatedData
{
    /**
     * Get the validated data.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null);
}
