<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'name',
        'description',
        'department_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'department_id' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
