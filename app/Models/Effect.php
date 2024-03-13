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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Effect extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

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

    public static function getForm(): array
    {
        return [
            Select::make('finding_id')
                ->relationship('finding', 'title')
                ->required(),
            TextInput::make('title')
                ->required()
                ->maxLength(250)
                ->columnSpanFull(),
            RichEditor::make('description')
                ->columnSpanFull(),
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
                        $data = Effect::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),
        ];
    }
}
