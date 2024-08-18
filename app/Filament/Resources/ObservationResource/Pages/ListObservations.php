<?php

namespace App\Filament\Resources\ObservationResource\Pages;

use App\Filament\Resources\ObservationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListObservations extends ListRecords
{
    protected static string $resource = ObservationResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Observations'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn(Builder $query) => $query->draft()),
            'in_review' => Tab::make('In review')
                ->modifyQueryUsing(fn(Builder $query) => $query->inReview()),
            'issued' => Tab::make('Issued')
                ->modifyQueryUsing(fn(Builder $query) => $query->issued()),
            'team_resolved' => Tab::make('Team Resolved')
                ->modifyQueryUsing(fn(Builder $query) => $query->teamResolved()),
            'reported' => Tab::make('Reported')
                ->modifyQueryUsing(fn(Builder $query) => $query->reported()),
            'da_resolved' => Tab::make('DA Resolved')
                ->modifyQueryUsing(fn(Builder $query) => $query->daResolved()),
            'ra_resolved' => Tab::make('RA Resolved')
                ->modifyQueryUsing(fn(Builder $query) => $query->raResolved()),
            'ag_resolved' => Tab::make('AG / DAG Resolved')
                ->modifyQueryUsing(fn(Builder $query) => $query->agResolved()),
            'pac_resolved' => Tab::make('PAC / Parliament Resolved')
                ->modifyQueryUsing(fn(Builder $query) => $query->pacResolved()),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver(),
        ];
    }
}
