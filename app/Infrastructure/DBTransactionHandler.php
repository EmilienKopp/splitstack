<?php

namespace App\Infrastructure;

use App\Application\Shared\Contracts\TransactionHandler;

final class DBTransactionHandler implements TransactionHandler
{
    public static function perform(callable $callback): mixed
    {
        return \DB::transaction($callback);
    }
}
