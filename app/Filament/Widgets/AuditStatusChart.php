<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AuditStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Audit Status';

    protected static ?int $sort = 3;

    // protected static ?string $maxHeight = '180px';
    protected static ?array $options = [];

    protected int|string|array $columnSpan = [
        // 'md' => 1,
        // 'sm' => 3,
        'lg' => 3,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        // $startDate = $this->filters['start_date'];
        // $endDate = $this->filters['end_date'];
        $institutions = $this->filters['institutions'];
        $districts = $this->filters['districts'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        // $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        $data = Audit::query()
            ->selectRaw('status, count(*) as count')
            // ->when($startDate, fn($query, $startDate) => $query->where('created_at', '>=', $startDate))
            // ->when($endDate, fn($query, $endDate) => $query->where('created_at', '<=', $endDate))
            ->when(
                $institutions,
                function ($query, $institutions) {
                    return $query->whereHas('institutions', function ($query) use ($institutions) {
                        $query->whereIn('id', $institutions);
                    });
                }
                // fn($query, $institutions) => $query->whereHas('regions', fn($query) => $query->where('id', 'in', $institutions))
            )
            ->when($districts, fn($query, $districts) => $query->whereHas(
                'districts',
                fn($query) => $query->whereIn('districts.id', $districts)
            ))
            ->when($auditStatus, fn($query, $auditStatus) => $query->where('status', $auditStatus))
            ->when($observationStatus, fn($query, $observationStatus) => $query->whereHas('observations', fn($query) => $query->where('status', $observationStatus)))
            ->when($findingType, function ($query, $findingType) {
                return $query->whereHas('findings', fn($query) => $query->where('type', $findingType));
            })
            // ->when($unitDepartment, function ($query, $unitDepartment) {
            //     // dd($unitDepartment);
            //     return $query->whereHas('reports', fn($query) => $query->where('section', $unitDepartment));
            // })
            ->groupBy('status')
            ->get();

        // ->mapWithKeys(fn ($item) => [$item->status => $item->count])
        // ->toArray();
        return [
            'datasets' => [
                [
                    // 'label' => 'Blog posts created',
                    'data' => $data->pluck('count'),
                    'backgroundColor' => $data->map(fn($item) => 'rgb(' . $item->status->getColor()['500'] . ')'),
                    // 'borderColor' => '#FF0000',
                    'borderWidth' => 0,
                    'animation' => [
                        'duration' => 1500,
                    ],
                ],
            ],
            'labels' => $data->map(fn($item) => $item->status->getLabel()),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Audit Status',
                        'padding' => 10,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
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
