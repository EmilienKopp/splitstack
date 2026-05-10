<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Money\Money as PHPMoney;

class Money implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?PHPMoney
    {
        if ($value === null) {
            return null;
        }

        if (! $model->hasAttribute('amount') || ! $model->hasAttribute('currency')) {
            throw new InvalidArgumentException('The model does not have the required attributes for Money casting.');
        }

        return new PHPMoney($value, new \Money\Currency($attributes['currency']));
    }

    public function set($model, string $key, $value, array $attributes): ?array
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof PHPMoney) {
            return [
                'amount' => $value->getAmount(),
                'currency' => $value->getCurrency()->getCode(),
            ];
        }

        // Raw value (e.g. from mass assignment) — pass through as-is.
        // The currency attribute will be set separately by fill().
        return ['amount' => $value];
    }
}
