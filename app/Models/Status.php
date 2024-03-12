<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'name',
        'description',
        'date',
    ];

    protected $casts = [
        'id' => 'integer',
        'date' => 'date',
    ];

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }

    public function observations(): BelongsToMany
    {
        return $this->belongsToMany(Observation::class);
    }

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(Finding::class);
    }

    public function recommendations(): BelongsToMany
    {
        return $this->belongsToMany(Recommendation::class);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class);
    }

    public function followUps(): BelongsToMany
    {
        return $this->belongsToMany(FollowUp::class);
    }
}
