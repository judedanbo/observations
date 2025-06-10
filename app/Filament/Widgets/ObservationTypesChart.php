<?php

namespace App\Filament\Widgets;

use App\Models\Finding;
use App\Models\Observation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ObservationTypesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    protected static ?string $heading = 'Finding by types';

    protected function getData(): array
    {
        // $startDate = $this->filters['start_date'];
        // $endDate = $this->filters['end_date'];
        $districts = $this->filters['districts'];
        $institutions = $this->filters['institutions'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        // $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        $data = Finding::query()
            // ->join('findings', 'observations.id', '=', 'findings.observation_id')
            ->selectRaw('type, count(*) as count')
            // ->when($startDate, fn($query, $startDate) => $query->where('created_at', '>=', $startDate))
            // ->when($endDate, fn($query, $endDate) => $query->where('created_at', '<=', $endDate))
            ->when(
                $auditStatus,
                function ($query, $auditStatus) {
                    $query->whereHas(
                        'observation.audit',
                        fn($query) => $query->where(
                            'status',
                            $auditStatus
                        )
                    );
                }
            )
            ->when(
                $districts,
                fn($query, $districts) => $query->whereHas(
                    'observation.audit',
                    fn($query) => $query->whereHas(
                        'districts',
                        fn($query) => $query->whereIn('districts.id', $districts)
                    )
                )
            )
            ->when(
                $observationStatus,
                function ($query, $observationStatus) {
                    $query->whereHas('observation', fn($query) => $query->where('status', $observationStatus));
                }
            )
            ->when(
                $findingType,
                function ($query, $findingType) {
                    $query->where('type', $findingType);
                }
            )
            // ->when(
            //     // $unitDepartment,
            //     function ($query, $unitDepartment) {
            //         $query->whereHas('observation.audit', function ($query) use ($unitDepartment) {
            //             $query->whereHas('reports', fn($query) => $query->where('section', $unitDepartment));
            //         });
            //     }
            // )
            ->groupBy('type')
            ->get();

        // 'rgb(' . $item->status->getColor()['500'] . ')'

        return [
            'datasets' => [
                [
                    'label' => 'Findings types',
                    'backgroundColor' => $data->map(fn($item) => 'rgb(' . $item->type->getColor()['500'] . ')'),
                    'data' => $data->map(fn($item) => $item->count),
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
            ],
            'labels' => $data->map(fn($item) => $item->type->getLabel()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
