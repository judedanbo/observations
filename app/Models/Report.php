<?php

namespace App\Models;

use App\Enums\AuditDepartmentEnum;
use App\Enums\AuditTypeEnum;
use App\Enums\FindingTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institution_id',
        'audit_id',
        'finding_id',
        'section',
        'paragraphs',
        'title',
        'type',
        'amount',
        'recommendation',
        'amount_recovered',
        'surcharge_amount',
        'implementation_date',
        'implementation_status',
        'comments',
    ];

    protected $casts = [
        'institution_id' => 'int',
        'audit_id' => 'int',
        'type' => FindingTypeEnum::class,
        'section' => AuditTypeEnum::class,
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    // PAC recommendation not audit recommendation
    public function recommendations(): HasManyThrough
    {
        return $this->hasManyThrough(Parliament::class, Finding::class, 'id', 'finding_id', 'finding_id', 'id');
    }

    public function recommend($data)
    {
        $this->recommendations()->create($data);
    }

    public function scopeMda()
    {
        return $this->where('section', AuditTypeEnum::MDA);
    }
    public function scopeDacf()
    {
        return $this->where('section', AuditTypeEnum::DACF);
    }

    public function scopeNational()
    {
        return $this->where('section', AuditTypeEnum::NATIONAL);
    }

    public function scopeIgf()
    {
        return $this->where('section', AuditTypeEnum::IGF);
    }
    public function scopePre()
    {
        return $this->where('section', AuditTypeEnum::PRE);
    }
    public function scopeState()
    {
        return $this->where('section', AuditTypeEnum::SEO);
    }
    public function scopeTertiary()
    {
        return $this->where('section', AuditTypeEnum::TERTIARY);
    }
    public function scopeBog()
    {
        return $this->where('section', AuditTypeEnum::BOG);
    }
    public function scopePerformance()
    {
        return $this->where('section', AuditTypeEnum::PERFORMANCE);
    }

    public function scopeSpecial()
    {
        return $this->where('section', AuditTypeEnum::SPECIAL);
    }
    public function scopeIs()
    {
        return $this->where('section', AuditTypeEnum::IS);
    }

    public static function getForm(): array
    {
        return [
            Select::make('institution_id')
                ->relationship('institution', 'name')
                ->createOptionForm(Institution::getForm())
                ->native(false)
                ->searchable()
                ->sortable()
                ->preload()
                ->required(),
            Select::make('audit_id')
                ->relationship('audit', 'title')
                ->createOptionForm(Audit::getForm())
                ->native(false)
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('paragraphs')
                ->required()
                ->maxLength(20),
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            TextInput::make('type')
                ->maxLength(255),
            TextInput::make('amount')
                ->numeric(),
            Textarea::make('recommendation')
                ->columnSpanFull(),
            TextInput::make('amount_recovered')
                ->numeric(),
            TextInput::make('surcharge_amount')
                ->numeric(),
            TextInput::make('implementation_date')
                ->maxLength(10),
            TextInput::make('implementation_status')
                ->maxLength(255),
            Textarea::make('comments')
                ->columnSpanFull(),
        ];
    }
}
