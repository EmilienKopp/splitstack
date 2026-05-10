<?php

namespace App\Domain\Finance\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class ExpenseApproved
{
    use Dispatchable;

    public function __construct(
        public int $expenseId,
        public int $approvedByUserId,
    ) {}
}
