<?php

namespace App\Filament\Resources\ParliamentResource\Pages;

use App\Enums\RecommendationStatusEnum;
use App\Filament\Resources\ParliamentResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListParliaments extends ListRecords
{
    protected static string $resource = ParliamentResource::class;

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
            'all' => Tab::make('All PAC Directives'),
            'opened' => Tab::make('Opened')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', RecommendationStatusEnum::OPEN)),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', RecommendationStatusEnum::CLOSE)),
        ];
    }
}
