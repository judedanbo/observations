<?php

namespace App\Models;

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
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
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

    public static function getForm(): array
    {
        return [
            Select::make('observation_id')
                ->relationship('observation', 'title')
                ->required()
                ->columnSpanFull(),
            Select::make('finding_id')
                ->relationship('finding', 'title')
                ->required()
                ->columnSpanFull(),
            Select::make('recommendation_id')
                ->relationship('recommendation', 'title')
                ->required()
                ->columnSpanFull(),
            TextInput::make('title')
                ->required()
                ->maxLength(250),
            RichEditor::make('description')
                ->columnSpanFull(),
            Actions::make([
                Action::make('save')
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
                        $data = FollowUp::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),

            // Actions::make([
            //     Action::make('Save')
            //         ->label('Generate data')
            //         ->icon('heroicon-m-arrow-path')
            //         ->outlined()
            //         ->color('gray')
            //         ->visible(function (string $operation) {
            //             if ($operation !== 'create') {
            //                 return false;
            //             }
            //             if (!app()->environment('local')) {
            //                 return false;
            //             }
            //             return true;
            //         })
            //         ->action(function ($livewire) {
            //             $data = Action::factory()->make()->toArray();
            //             $livewire->form->fill($data);
            //         }),
            // ])
        ];
    }
}
