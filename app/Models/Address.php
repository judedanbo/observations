<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'street',
        'city',
        'region',
        'country',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }
}
