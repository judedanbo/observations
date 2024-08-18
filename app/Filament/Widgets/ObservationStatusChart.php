<?php

namespace App\Filament\Widgets;

use App\Models\Observation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ObservationStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Observation status';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'md' => 2,
        // 'xl' => 3,
    ];

    protected function getData(): array
    {
        $startDate = $this->filters['start_date'];
        $endDate = $this->filters['end_date'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        $data = Observation::query()
            ->selectRaw('observations.status, count(*) as count')
            // ->join('audits', 'observations.audit_id', '=', 'audits.id')
            ->when($startDate, fn ($query, $startDate) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn ($query, $endDate) => $query->where('created_at', '<=', $endDate))
            ->when(
                $auditStatus,
                function ($query, $auditStatus) {
                    $query->whereHas('audit', fn ($query) => $query->where('status', $auditStatus));
                }
            )
            ->when(
                $observationStatus,
                function ($query, $observationStatus) {
                    $query->where('status', $observationStatus);
                }
            )
            ->when(
                $findingType,
                function ($query, $findingType) {
                    $query->whereHas('findings', fn ($query) => $query->where('type', $findingType));
                }
            )
            ->when(
                $unitDepartment,
                function ($query, $unitDepartment) {
                    $query->whereHas('audit', function ($query) use ($unitDepartment) {
                        $query->whereHas('reports', fn ($query) => $query->whereIn('section', $unitDepartment));
                    });
                }
            )
            ->groupBy('observations.status')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Observation status',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $data->map(fn ($item) => 'rgb('.$item->status->getColor()['500'].')'),
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
            ],
            'labels' => $data->map(fn ($item) => $item->status->getLabel()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
