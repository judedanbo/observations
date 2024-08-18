<?php

namespace App\Models;

use App\Enums\RecommendationStatusEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parliament extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'finding_id',
        'pac_directive',
        'pac_directive_date',
        'client_responsible_officer',
        'gas_assigned_officer',
        'implementation_date',
        'completed_by_date',
        'status',
    ];

    protected $casts = [
        'finding_id' => 'int',
        'pac_directive_date' => 'date',
        'implementation_date' => 'date',
        'completed_by_date' => 'date',
        'status' => RecommendationStatusEnum::class,
    ];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function markAsCompleted(): void
    {
        $this->status = RecommendationStatusEnum::Closed;
        $this->completed_by_date = now();
        $this->implementation_date = now();
        $this->save();
    }

    public static function getForm($finding = null): array
    {
        return [
            Select::make('finding_id')
                ->relationship('finding', 'title')
                ->visible($finding === null)
                ->default($finding ?? null)
                ->required(),
            TextInput::make('pac_directive')
                ->required()
                ->columnSpanFull()
                ->maxLength(255),
            TextInput::make('client_responsible_officer'),
            TextInput::make('gas_assigned_officer')
                ->columnStart(1),
            DatePicker::make('pac_directive_date')
                // ->columnStart(1)
                ->default(now()),
            // DatePicker::make('implementation_date'),

        ];
    }
}
