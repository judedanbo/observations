<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'observation_id',
        'follow_up_id',
        'finding_id',
        'recommendation_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'follow_up_id' => 'integer',
        'finding_id' => 'integer',
        'recommendation_id' => 'integer',
    ];

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(Observation::class);
    }

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }
}
