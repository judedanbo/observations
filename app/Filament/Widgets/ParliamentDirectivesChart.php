<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ParliamentDirectivesChart extends ChartWidget
{
    protected static ?string $heading = 'PAC directives by year';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = [
        'md' => 3,
        // 'xl' => 3,
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Number of directives',
                    'data' => [232, 123, 321],
                    'borderColor' => 'rgb(54, 162, 235)',
                    // 'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Open Directives',
                    'data' => [122, 83, 191],
                    'borderColor' => 'rgb(255, 99, 132)',
                    // 'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Closed Directives',
                    'data' => [24, 50, 200],

                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => '0.1',
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
        return 'line';
    }
}
