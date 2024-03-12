<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'title',
        'description',
        'file',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
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

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function followUps(): BelongsToMany
    {
        return $this->belongsToMany(FollowUp::class);
    }
}
