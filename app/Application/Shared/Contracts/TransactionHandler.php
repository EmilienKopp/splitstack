<?php

namespace App\Application\Shared\Contracts;

interface TransactionHandler
{
    /**
     * Handle a transaction.
     */
    public static function perform(callable $callback): mixed;
}
