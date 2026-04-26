<?php

namespace App\Application\Shared\Contracts;

use ArrayAccess;

/**
 * @extends ArrayAccess<int|string, mixed>
 */
interface DTO extends ArrayAccess
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static;
}
