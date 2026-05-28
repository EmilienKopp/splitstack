<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\Contracts\TransactionHandler;
use DB;

final class DBTransactionHandler implements TransactionHandler
{
    public static function perform(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
