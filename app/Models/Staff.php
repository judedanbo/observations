<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'name',
        'staff_number',
        'email',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            // Group::make()
            // ->schema([
            TextInput::make('staff_number')
                ->required()
                ->maxLength(10),
            TextInput::make('email')
                ->suffix('@audit.gov.gh')
                ->required()
                ->maxLength(255),
            // ])
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
            //             $data = Staff::factory()->make()->toArray();
            //             $livewire->form->fill($data);
            //         }),
            // ])
            //     ->label('Actions')
            //     ->columnSpanFull(),
        ];
    }
}
