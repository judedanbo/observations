<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'observation_id',
        'finding_id',
        'recommendation_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'observation_id' => 'integer',
        'action_id' => 'integer',
        'finding_id' => 'integer',
        'recommendation_id' => 'integer',
    ];

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(Observation::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public static function getForm(?int $findingId = null): array
    {
        return [
            Select::make('observation_id')
                ->live()
                ->relationship(
                    name: 'observation',
                    titleAttribute: 'title',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        $query->when(
                            $get('finding_id'),
                            fn (Builder $query, $auditId) => $query->whereHas(
                                'findings',
                                fn (Builder $query) => $query->where('id', $auditId)
                            )
                        );
                    }
                )
                ->editOptionForm(Observation::getForm())
                ->searchable()
                ->searchPrompt('Search observations...')
                ->noSearchResultsMessage('No observations found.')
                ->loadingMessage('Loading observations...')
                ->placeholder('Select or search for an observation')
                ->preload()
                ->required()
                ->columnSpanFull(),
            Select::make('finding_id')
                ->relationship(
                    name: 'finding',
                    titleAttribute: 'title',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        $query->when(
                            $get('observation_id'),
                            fn (Builder $query, $observationId) => $query->where('observation_id', $observationId)
                        );
                        $query->when(
                            $get('recommendation_id'),
                            fn (Builder $query, $recommendationId) => $query->whereHas('recommendations', fn (Builder $query) => $query->where('id', $recommendationId))
                        );
                    }
                )
                ->editOptionForm(Finding::getForm())
                ->searchable()
                ->searchPrompt('Search audit findings...')
                ->noSearchResultsMessage('No audit findings found.')
                ->loadingMessage('Loading audit findings...')
                ->placeholder('Select or search for a finding')
                ->preload()
                ->required()
                ->columnSpanFull(),
            Select::make('recommendation_id')
                ->relationship(
                    name: 'recommendation',
                    titleAttribute: 'title',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        $query->when($get('finding_id'), fn (Builder $query, $findingId) => $query->where('finding_id', $findingId));
                    }
                )
                ->required()
                ->editOptionForm(Recommendation::getForm())
                ->searchable()
                ->searchPrompt('Search recommendations...')
                ->noSearchResultsMessage('No recommendations found.')
                ->loadingMessage('Loading observations...')
                ->placeholder('Select or search for a recommendation')
                ->preload()
                ->columnSpanFull(),
            TextInput::make('title')
                ->required()
                ->maxLength(250),
            RichEditor::make('description')
                ->columnSpanFull(),
        ];
    }
}
