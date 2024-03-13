<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leader extends Model
{
    use HasFactory, SoftDeletes, LogAllTraits;

    protected $fillable = [
        'staff_number',
        'name',
        'title',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('staff_number')
                ->maxLength(15),
            TextInput::make('name')
                ->required()
                ->maxLength(250),
            TextInput::make('title')
                ->required()
                ->maxLength(250),
            DatePicker::make('start_date')
                ->native(false)
                ->required(),
            DatePicker::make('end_date')
                ->native(false),
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
                        $data = Leader::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),
        ];
    }
}
