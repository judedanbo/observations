<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class YearOnYearObservationsChart extends ChartWidget
{
    protected static ?string $heading = 'Year on year finding type';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'md' => 2,
        // 'xl' => 3,
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Financial',
                    'data' => [232, 123, 321],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Compliance',
                    'data' => [523, 321, 123],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Internal control',
                    'data' => [213, 215, 132],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
            ],
            'labels' => ['2022', '2023', '2023'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
