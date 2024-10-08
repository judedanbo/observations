<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Effect extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'finding_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'finding_id' => 'integer',
    ];

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public static function getForm(?int $findingId = null): array
    {
        return [
            Select::make('finding_id')
                ->relationship('finding', 'title')
                ->editOptionForm(Finding::getForm())
                ->searchable()
                ->searchPrompt('Search findings...')
                ->noSearchResultsMessage('No findings found.')
                ->loadingMessage('Loading findings...')
                ->placeholder('Select for search for an audit finding')
                ->preload()
                ->hidden(function () use ($findingId) {
                    return $findingId !== null;
                })
                ->required(),
            TextInput::make('title')
                ->required()
                ->maxLength(250)
                ->columnSpanFull(),
            RichEditor::make('description')
                ->columnSpanFull(),
        ];
    }
}
