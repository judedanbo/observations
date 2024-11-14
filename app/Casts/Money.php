<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // dd($value);
        if ($attributes['amount'] === null || $attributes['amount'] === '') {

            return null;
        }

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, 'GH¢ ');
        $formatter->setSymbol(\NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL, ',');
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
        return \Brick\Money\Money::ofMinor($attributes['amount'], 'USD')->formatWith($formatter);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof \Brick\Money\Money) {
            return $value;
        }
        return $value->getMinorAmount()->toInt();
    }
}
