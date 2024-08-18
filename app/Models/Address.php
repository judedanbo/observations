<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'street',
        'city',
        'region',
        'country',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('street')
                ->required()
                ->maxLength(250),
            TextInput::make('city')
                ->required()
                ->maxLength(250),
            TextInput::make('region')
                ->required()
                ->maxLength(3),
            TextInput::make('country')
                ->required()
                ->maxLength(250),
        ];
    }
}
