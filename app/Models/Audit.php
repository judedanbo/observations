<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'title',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'year',
    ];

    protected $casts = [
        'id' => 'integer',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }
}
