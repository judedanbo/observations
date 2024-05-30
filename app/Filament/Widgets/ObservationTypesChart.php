<?php

namespace App\Filament\Widgets;

use App\Models\Finding;
use App\Models\Observation;
use Filament\Widgets\ChartWidget;

class ObservationTypesChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Finding by types';

    protected function getData(): array
    {
        $data  =  Finding::query()
            // ->join('findings', 'observations.id', '=', 'findings.observation_id')
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        // dd($data->map(fn ($item) =>  $item->type->getColor()));

        // 'rgb(' . $item->status->getColor()['500'] . ')'


        return [
            'datasets' => [
                [
                    'label' => 'Findings types',
                    'backgroundColor' => $data->map(fn ($item) => 'rgb(' . $item->type->getColor()['500'] . ')'),
                    'data' => $data->map(fn ($item) => $item->count),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->map(fn ($item) => $item->type->getLabel()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
