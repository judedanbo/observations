<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recovery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'finding_id',
        'amount',
        'comments'
    ];


    protected $casts = [
        'amount' =>  'decimal:2',
    ];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public static function getForm(int|null $observationId = null): array
    {
        return [
            Select::make('finding_id')
                ->relationship('findings', 'title')
                ->searchable()

        ];
    }
}
