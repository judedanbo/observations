<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leader extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_number',
        'name',
        'title',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }
}
