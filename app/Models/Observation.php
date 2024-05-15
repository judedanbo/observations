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
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'audit_id', // 'audit_id' is the foreign key
        'title',
        'criteria',
    ];

    protected $casts = [
        'id' => 'integer',
        'audit_id' => 'integer',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function auditActions(): BelongsToMany
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

    public function recommendations(): HasManyThrough
    {
        return $this->hasManyThrough(Recommendation::class, Finding::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public static function getForm($auditId = null): array
    {
        return [
            Select::make('audit_id')
                ->hidden(function () use ($auditId) {
                    return $auditId !== null;
                })
                ->relationship('audit', 'title')
                ->required(),
            TextInput::make('title')
                ->required()
                ->live()
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
