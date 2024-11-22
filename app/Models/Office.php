<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
                ->options(function (Get $get): Collection {
                    if ($get('district_id')) {
                        return Region::query()
                            ->whereHas('districts', function (Builder $query) use ($get) {
                                $query->where('id', $get('district_id'));
                            })
                            ->pluck('name', 'id');
                    }
                    return Region::query()->pluck('name', 'id');
                    // return $query->from('regions')->pluck('name', 'id');
                })
                ->live()
                ->searchable()
                ->preload()
                ->native(false)
                // ->default('1')
                ->afterStateUpdated(fn(Set $set) => $set('district_id', null)),
            Select::make('district_id')
                ->relationship(
                    name: 'district',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query, Get $get) {
                        if (!$get('region')) {
                            return $query;
                        }
                        return $query->where('region_id', $get('region'));
                    }
                )
                ->live()
                ->native(false)
                ->preload()
                ->searchable()
                ->afterStateUpdated(
                    function (Set $set, Get $get) {
                        $set('region', Region::query()
                            ->whereHas('districts', function (Builder $query) use ($get) {
                                $query->where('districts.id', $get('district_id'));
                            })
                            ->first()?->id);
                    }
                )
                ->required(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),
        ];
    }
}
