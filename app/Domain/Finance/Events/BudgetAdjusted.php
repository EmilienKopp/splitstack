<?php

namespace App\Domain\Finance\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class BudgetAdjusted
{
    use Dispatchable;

    public function __construct(
        public int $budgetId,
        public int $adjustmentId,
        public float $adjustmentAmount,
    ) {}
}
