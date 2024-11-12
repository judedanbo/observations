<?php

namespace App\Filament\Resources\SurchargeResource\Pages;

use App\Filament\Resources\SurchargeResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSurcharges extends ListRecords
{
    protected static string $resource = SurchargeResource::class;

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
            'all' => Tab::make('All Findings'),
            'financial' => Tab::make('Financial')
                ->modifyQueryUsing(fn(Builder $query) => $query->financial()),
            'internal_control' => Tab::make('Internal Control')
                ->modifyQueryUsing(fn(Builder $query) => $query->control()),
            'compliance' => Tab::make('Compliance')
                ->modifyQueryUsing(fn(Builder $query) => $query->compliance()),
        ];
    }
}
