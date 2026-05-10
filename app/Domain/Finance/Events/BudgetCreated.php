<?php

namespace App\Domain\Finance\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class BudgetCreated
{
    use Dispatchable;

    public function __construct(
        public int $budgetId,
        public int $projectId,
        public float $amount,
        public string $currency,
    ) {}
}
