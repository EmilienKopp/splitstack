<?php

declare(strict_types=1);

namespace App\Application\Shared\Contracts;

interface TransactionHandler
{
    /**
     * Handle a transaction.
     */
    public static function perform(callable $callback): mixed;
}
