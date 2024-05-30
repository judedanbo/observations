<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

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
        'follow_up_id' => 'integer',
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

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public static function getForm($observationId = null): array
    {
        return [
            Select::make('observation_id')
                ->live()
                ->relationship(
                    name: 'observation',
                    titleAttribute: 'title',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        $query->when($get('finding_id'), fn (Builder $query, $findingId) => $query->whereHas('findings', fn (Builder $query) => $query->where('id', $findingId)));
                    }
                )
                ->default($observationId)
                ->editOptionForm(Observation::getForm())
                ->searchable()
                ->searchPrompt('Search observation...')
                ->noSearchResultsMessage('No observation found.')
                ->loadingMessage('Loading observation...')
                ->placeholder('Select for search for an observation')
                ->preload()
                ->required()
                ->columnSpanFull(),
            // Select::make('follow_up_id')
            //     ->relationship('followUp', 'title')
            //     ->required(),
            Select::make('finding_id')
                ->live()
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
                ->searchPrompt('Search findings...')
                ->noSearchResultsMessage('No findings found.')
                ->loadingMessage('Loading findings...')
                ->placeholder('Select for search for an audit finding')
                ->preload()
                ->required()
                ->columnSpanFull(),
            Select::make('recommendation_id')
                ->relationship(name: 'recommendation', titleAttribute: 'title', modifyQueryUsing: function (Builder $query, Get $get) {
                    $query->when($get('finding_id'), fn (Builder $query, $findingId) => $query->where('finding_id', $findingId));
                })
                ->editOptionForm(Recommendation::getForm())
                ->searchable()
                ->searchPrompt('Search recommendation...')
                ->noSearchResultsMessage('No recommendation found.')
                ->loadingMessage('Loading recommendation...')
                ->placeholder('Select for search for an audit recommendation')
                ->preload()
                ->required()
                ->columnSpanFull(),
            TextInput::make('title')
                ->required()
                ->maxLength(250)
                ->columnSpanFull(),
            RichEditor::make('description')
                ->columnSpanFull(),
            Actions::make([
                ActionsAction::make('Save')
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
                        $data = Action::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull()
        ];
    }
}
