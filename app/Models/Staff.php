<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'name',
        'staff_number',
        'email',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }
}
