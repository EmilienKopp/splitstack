<?php

declare(strict_types=1);

namespace App\Application\Shared\Contracts;

interface HasValidatedData
{
    /**
     * @return array<string, mixed>
     */
    public function validated(): array;
}
