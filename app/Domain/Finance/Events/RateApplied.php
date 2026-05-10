<?php

namespace App\Domain\Finance\Events;

use Illuminate\Foundation\Bus\Dispatchable;

class RateApplied
{
    use Dispatchable;

    public function __construct(
        public int $rateId,
        public int $clockEntryId,
        public float $appliedAmount,
        public string $currency,
    ) {}
}
