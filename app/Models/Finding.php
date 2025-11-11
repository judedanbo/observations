<?php

namespace App\Models;

use App\Enums\FindingClassificationEnum;
use App\Enums\FindingTypeEnum;
use App\Http\Traits\LogAllTraits;
use Brick\Money\Money as MoneyMoney;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finding extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    public $formatter;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $this->formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, 'GH¢ ');
        $this->formatter->setSymbol(\NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL, ',');
        $this->formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
    }

    protected $fillable = [
        'title',
        'description',
        'observation_id',
        'type',
        'amount',
        'surcharge_amount',
        // 'total_recoveries',
        'classification',
        'amount_resolved',
        'amount_resolved',

    ];

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'type' => FindingTypeEnum::class,
        'amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
        'total_recoveries' => 'decimal:2',
        'amount_resolved' => 'decimal:2',
        'outstanding' => 'decimal:2',
        'classification' => FindingClassificationEnum::class,
    ];

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function status(): HasOne
    {
        return $this->hasMany(Status::class)->latestOfMany();
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

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function recoveries(): HasMany
    {
        return $this->hasMany(Recovery::class);
    }

    public function directives(): HasMany
    {
        return $this->hasMany(Parliament::class);
    }

    public function getAmountDisplayAttribute()
    {
        return MoneyMoney::of($this->amount ?? 0, 'USD')
            ->formatWith($this->formatter);
    }

    // public function getAmountResolvedDisplayAttribute()
    // {
    //     return $this->amount_display;
    // }

    public function getTotalRecoveriesDisplayAttribute()
    {
        return MoneyMoney::of($this->total_recoveries ?? 0, 'USD')
            ->formatWith($this->formatter);
    }

    public function getTotalRecoveriesAttribute()
    {
        return $this->recoveries()->sum('amount');
    }

    public function getOutstandingAttribute()
    {
        $outstanding = ($this->amount) + ($this->surcharge_amount ?? 0)
            - ($this->amount_resolved ?? 0) - ($this->total_recoveries ?? 0);

        return MoneyMoney::of($outstanding ?? 0, 'USD')
            ->formatWith($this->formatter);
    }

    public function getAmountResolvedDisplayAttribute()
    {
        return MoneyMoney::of($this->amount_resolved ?? 0, 'USD')
            ->formatWith($this->formatter);
    }

    public function scopeFinancial(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::FIN);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function addDocuments($data)
    {
        // dd('findings');
        $document = new Document;
        $document->title = $data['title'] ?? 'Evidence for '.$this->title;
        $document->description = $data['description'] ?? 'Evidence for '.$this->title;
        $document->file = $data['file'];

        $this->documents()->save($document);
    }

    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(AuditorGeneralReport::class)
            ->withPivot('report_section_order')
            ->withTimestamps();
    }

    public function scopeControl(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::INT);
    }

    public function scopeCompliance(Builder $query): Builder
    {
        return $query->where('type', FindingTypeEnum::COM);
    }

    public function getSurchargeAmountDisplayAttribute(?float $amount = null)
    {

        return MoneyMoney::of($this->surcharge_amount ?? 0, 'USD')
            ->formatWith($this->formatter);
    }

    public function getAmountDueAttribute()
    {
        $amount = ($this->amount ?? 0) + ($this->surcharge_amount ?? 0) - ($this->amount_resolved ?? 0);

        return MoneyMoney::of($amount, 'USD')
            ->formatWith($this->formatter);
    }

    public function getAmountDueIntAttribute()
    {
        return ($this->amount ?? 0) + ($this->surcharge_amount ?? 0) - ($this->amount_resolved ?? 0);
    }

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
                ]),

        ];
    }
}
