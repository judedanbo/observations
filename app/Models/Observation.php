<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'title',
        'criteria',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(250),
            RichEditor::make('criteria')
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
            //             $data = Observation::factory()->make()->toArray();
            //             $livewire->form->fill($data);
            //         }),
            // ])
            //     ->label('Actions')
            //     ->columnSpanFull(),
        ];
    }
}
