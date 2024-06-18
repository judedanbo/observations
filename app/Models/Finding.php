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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;

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
    public function scopeFinancial(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::FIN);
    }
    public function scopeControl(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::INT);
    }
    public function scopeCompliance(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::COM);
    }

    // public function surcharge(?float $amount = null)
    // {
    //     if ($amount) {
    //         $this->surcharge_amount = $amount;
    //         $this->save();
    //     }
    // }

    // public function recover($data)
    // {
    //     $this->recoveries()->create($data);
    //     $this->save();
    // }
    // public function addCause($data)
    // {
    //     $this->causes()->create($data);
    //     $this->save();
    // }
    // public function addEffect($data)
    // {
    //     $this->effects()->create($data);
    //     $this->save();
    // }

    public static function getForm(?int $observationId = null): array
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
                ->required()
                ->columnSpan(1),
            TextInput::make('title')
                ->required()
                ->columnSpanFull()
                ->maxLength(250),
            Textarea::make('description')
                ->label('Details')
                ->columnSpanFull(),
            Fieldset::make('Amounts/Surcharge')
                ->schema([
                    TextInput::make('amount')
                        ->numeric()
                        ->minValue(0.01),
                    TextInput::make('surcharge_amount')
                        ->numeric()
                        ->minValue(0.01),
                ])

        ];
    }
}
