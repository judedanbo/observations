<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'short_name',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name',
            'short_name',
            'description',
        ]);
    }

}
