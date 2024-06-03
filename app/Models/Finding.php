<?php

namespace App\Models;

use App\Enums\FindingTypeEnum;
use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Observation;
use Filament\Forms\Components\Textarea;

class Finding extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'title',
        'description',
        'observation_id',
        'type',
        'amount',
        'surcharge_amount'
    ];

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'type' => FindingTypeEnum::class,
        'amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2'
    ];

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(Observation::class);
    }

    public function causes(): HasMany
    {
        return $this->hasMany(Cause::class);
    }

    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(Recovery::class);
    }

    public function directives(): HasMany
    {
        return $this->hasMany(Parliament::class);
    }

    public function getRecoveriesSumAttribute()
    {
        return $this->recoveries()->sum('amount');
    }

    public static function getForm(int|null $observationId = null): array
    {
        return [
            Select::make('observation_id')
                ->relationship('observation', 'title')
                ->editOptionForm(Observation::getForm())
                ->searchable()
                ->searchPrompt('Search observations...')
                ->noSearchResultsMessage('No observations found.')
                ->loadingMessage('Loading observations...')
                ->placeholder('Select for search for a observation')
                ->preload()
                ->hidden(function () use ($observationId) {
                    return $observationId !== null;
                })
                ->required(),
            Select::make('type')
                ->enum(FindingTypeEnum::class)
                ->options(FindingTypeEnum::class)
                ->native(false)
                ->label('Select finding Type')
                ->required(),
            TextInput::make('title')
                ->required()
                ->columnSpanFull()
                ->maxLength(250),
            Textarea::make('description')
                ->columnSpanFull(),
            TextInput::make('amount')
                ->numeric()
                ->minValue(0.01),
            TextInput::make('surcharge_amount')
                ->numeric()
                ->minValue(0.01),
            Actions::make([
                Action::make('Save')
                    ->label('Generate data')
                    ->icon('heroicon-m-arrow-path')
                    ->outlined()
                    ->color('gray')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }
                        if (!app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Finding::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),
        ];
    }
}
