<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;

class ObservationStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        $data  =  Observation::query()
            ->selectRaw('observations.status, audits.year, count(*) as count')
            ->join('audits', 'observations.audit_id', '=', 'audits.id')
            ->groupBy('audits.year', 'observations.status')
            ->get();
        // dd($data->toArray());
        return [
            'datasets' => [
                [
                    'label' => 'Draft',
                    'data' => $data->pluck('count'),
                    // 'backgroundColor' => $data->map(fn ($item) => 'rgb(' . $item->status->getColor()['500'] . ')'),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->map(fn ($item) => $item->year),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
