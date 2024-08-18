<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class YearOnYearAuditTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Year on year audit recommendation by audit type';

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = [
        'md' => 6,
        // 'xl' => 3,
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'MDAs',
                    'data' => [232, 123, 321],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'National Accounts',
                    'data' => [523, 321, 123],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'MMDA DACF',
                    'data' => [213, 215, 132],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'MMDA IGF',
                    'data' => [213, 215, 132],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Pre tertiary',
                    'data' => [63, 151, 129],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Statutory Boards and corporations',
                    'data' => [69, 125, 298],
                    'backgroundColor' => 'rgba(75, 192, 92, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Tertiary institutions',
                    'data' => [79, 125, 182],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Foreign receipts and payments BOG',
                    'data' => [123, 185, 92],
                    'backgroundColor' => 'rgba(75, 92, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Performance Audits',
                    'data' => [300, 59, 138],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Special audits',
                    'data' => [198, 15, 82],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
                [
                    'label' => 'Information system audit',
                    'data' => [25, 52, 62],
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
