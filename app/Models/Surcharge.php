<?php

namespace App\Models;

use App\Enums\FindingTypeEnum;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([Scopes\SurchargeScope::class])]
class Surcharge extends Model
{
    protected $table = 'findings';

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'type' => FindingTypeEnum::class,
        'amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
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

    public function surcharge($amount)
    {
        $this->surcharge_amount = $amount[0];
        $this->save();
    }
}
