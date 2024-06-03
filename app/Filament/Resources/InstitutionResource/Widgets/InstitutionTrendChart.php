<?php

namespace App\Filament\Resources\InstitutionResource\Widgets;

use Filament\Widgets\ChartWidget;

class InstitutionTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
