<?php

namespace App\Domain\Finance\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class ExpenseSubmitted
{
    use Dispatchable;

    public function __construct(
        public int $expenseId,
        public int $userId,
        public float $amount,
        public string $currency,
    ) {}
}
