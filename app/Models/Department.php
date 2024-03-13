<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'name',
        'short_name',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public static function getForm(): array
    {
        return [

            TextInput::make('name')
                ->required()
                ->maxLength(100),
            TextInput::make('short_name')
                ->maxLength(10),
            TextInput::make('description')
                ->maxLength(255)
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
                        $data = Department::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),
        ];
    }
}
