<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Widgets\ChartWidget;

class AuditStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Audit Status';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';
    protected static ?array $options = [];

    protected function getData(): array
    {
        $data  =  Audit::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
        // ->mapWithKeys(fn ($item) => [$item->status => $item->count])
        // ->toArray();
        // dd($data->map(fn ($item) => 'rgb(' . $item->status->getColor()['500'] . ')'));
        return [
            'datasets' => [
                [
                    // 'label' => 'Blog posts created',
                    'data' => $data->pluck('count'),
                    'backgroundColor' => $data->map(fn ($item) => 'rgb(' . $item->status->getColor()['500'] . ')'),
                    // 'borderColor' => '#FF0000',
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->map(fn ($item) => $item->status->getLabel()),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'datalabels' => [
                    'display' => true,
                    'color' => '#fff',
                    'font' => [
                        'weight' => 'bold',
                    ],
                    // 'formatter' => fn ($value, $context) => $value,
                ],
            ],
            'scales' => [
                'y' => [
                    'display' => false,
                    'ticks' => [
                        'display' => false,
                    ],
                ],
                'x' => [
                    'display' => false,
                    'ticks' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
