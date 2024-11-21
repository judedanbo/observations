<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'district_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'district_id' => 'integer',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    function region(): BelongsTo
    {
        return $this->belongsToThrough(Region::class, District::class);
    }

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }

    public static function getForm(): array
    {
        return [
            Select::make('region')
                ->options(Region::all()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->native(false)
                ->createOptionForm(Region::getForm()),
            Select::make('district_id')
                ->relationship('district', 'name')
                ->native(false)
                ->preload()
                ->searchable()
                ->createOptionForm(District::getForm())
                ->required(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),
        ];
    }
}
