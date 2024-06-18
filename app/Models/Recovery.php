<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public static function getForm(?int $findingId = null): array
    {
        return [
            Select::make('finding_id')
                ->relationship('findings', 'title')
                ->searchable()
                ->hidden(function () use ($findingId) {
                    return $findingId !== null;
                }),
            TextInput::make('amount')
                ->type('number')
                ->label('Amount recovered')
                ->minValue(0)
                ->step(0.01)
                ->required(),
            TextInput::make('comments')
                ->columnSpanFull()
        ];
    }
}
