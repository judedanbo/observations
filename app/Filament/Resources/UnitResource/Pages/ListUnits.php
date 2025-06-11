<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Units'),
            'greater_accra' => Tab::make('Greater Accra')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(2)
                )),
            'ashanti' => Tab::make('Ashanti')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(1)
                )),
            'central' => Tab::make('Central')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(6)
                )),
            'eastern' => Tab::make('Eastern')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(7)
                )),
            'western' => Tab::make('Western')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(15)
                )),
            'volta' => Tab::make('Volta')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(14)
                )),
            'ahafo' => Tab::make('Ahafo')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(3)
                )),
            'bono' => Tab::make('Bono')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(4)
                )),
            'bono_east' => Tab::make('Bono East')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(5)
                )),
            'northern' => Tab::make('Northern')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(9)
                )),
            'oti' => Tab::make('Oti')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(10)
                )),
            'western_north' => Tab::make('Western North')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(16)
                )),
            'north_east' => Tab::make('North East')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(8)
                )),
            'savannah' => Tab::make('Savannah')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->where('regions.id', 11)
                )),
            'upper_east' => Tab::make('Upper East')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(12)
                )),
            'upper_west' => Tab::make('Upper West')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas(
                    'office.district.region',
                    fn(Builder $query) => $query->whereId(13)
                )),
        ];
    }
}
