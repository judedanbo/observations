<?php

namespace App\Models;

use App\Casts\Money;
use App\Casts\SurchargeCast;
use App\Enums\FindingTypeEnum;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// #[ScopedBy([Scopes\SurchargeScope::class])] // scope surcharges to be displayed when surcharge_amount > 0
class Surcharge extends Model
{
    // TODO create a table for surcharges
    protected $table = 'findings';

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'type' => FindingTypeEnum::class,
        'amount' => Money::class,
        'surcharge_amount' => SurchargeCast::class,
    ];

    public function observation(): BelongsTo
    {
        return $this->belongsTo(Observation::class);
    }

    public function scopeFinancial(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::FIN);
    }

    public function scopeControl(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::INT);
    }

    public function scopeCompliance(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::COM);
    }

    public function surcharge($amount): void
    {
        $this->surcharge_amount = $amount[0];
        $this->save();
    }
}
