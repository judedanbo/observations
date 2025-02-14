<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\Institution;
use App\Models\Observation;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getColumns(): int
    {
        $count = count($this->getCachedStats());

        if ($count < 3) {
            return 3;
        }

        if (($count % 3) !== 1) {
            return 3;
        }

        return 4;
    }

    protected function getStats(): array
    {
        // $startDate = $this->filters['start_date'];
        // $endDate = $this->filters['end_date'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        // $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        $auditStats = Trend::model(Audit::class)
            ->between(
                start: now()->startOfMonth(30),
                end: now()
            )
            ->perDay()
            ->count()
            ->pluck('aggregate')
            ->toArray();
        $observationStats = Trend::model(Observation::class)
            ->between(
                start: now()->startOfMonth(30),
                end: now()
            )
            ->perDay()
            ->count()
            ->pluck('aggregate')
            ->toArray();

        $institutionStats = Trend::model(Institution::class)
            ->between(
                start: now()->startOfMonth(30),
                end: now()
            )
            ->perDay()
            ->count()
            ->pluck('aggregate')
            ->toArray();

        $recoveryStats = Trend::model(Finding::class)
            ->between(
                start: now()->startOfMonth(30),
                end: now()
            )
            ->perDay()
            ->sum('amount')
            ->pluck('aggregate')
            ->toArray();

        return [
            Stat::make('Clients', Number::format(
                Institution::query()
                    // ->when($unitDepartment, function ($query, $unitDepartment) {
                    //     return $query->whereHas('audits', fn($query) => $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment)));
                    // })
                    ->count()
            ))
                ->description('Clients')
                ->color('success')
                ->chart($institutionStats),
            Stat::make('Total Audits', Number::format(
                Audit::query()
                    // ->when($startDate, fn($query, $startDate) => $query->where('created_at', '>=', $startDate))
                    // ->when($endDate, fn($query, $endDate) => $query->where('created_at', '<=', $endDate))
                    ->when($auditStatus, fn($query, $auditStatus) => $query->where('status', $auditStatus))
                    ->when($observationStatus, fn($query, $observationStatus) => $query->whereHas('observations', fn($query) => $query->where('status', $observationStatus)))
                    ->when($findingType, function ($query, $findingType) {
                        return $query->whereHas('findings', fn($query) => $query->where('type', $findingType));
                    })
                    // ->when($unitDepartment, function ($query, $unitDepartment) {
                    //     return $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment));
                    // })
                    ->count()
            ))
                ->description('Total number of audits')
                ->color('success')
                ->chart($auditStats),
            Stat::make('Observations', Number::format(
                Observation::query()
                    // ->when($startDate, function ($query, $startDate) {
                    //     $query->whereHas('audit', function ($query) use ($startDate) {
                    //         $query->where('created_at', '>=', $startDate);
                    //     });
                    // })
                    // ->when($endDate, function ($query, $endDate) {
                    //     $query->whereHas('audit', function ($query) use ($endDate) {
                    //         $query->where('created_at', '<=', $endDate);
                    //     });
                    // })
                    ->when(
                        $auditStatus,
                        function ($query, $auditStatus) {
                            $query->whereHas('audit', fn($query) => $query->where('status', $auditStatus));
                        }
                    )
                    ->when(
                        $findingType,
                        function ($query, $findingType) {
                            $query->whereHas('findings', fn($query) => $query->where('type', $findingType));
                        }
                    )
                    // ->when(
                    //     $unitDepartment,
                    //     function ($query, $unitDepartment) {
                    //         $query->whereHas('audit', function ($query) use ($unitDepartment) {
                    //             $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment));
                    //         });
                    //     }
                    // )
                    ->count()
            ))
                ->description('Total number of observations')
                ->color('success')
                ->chart($observationStats),
            Stat::make('Amount', Number::format(
                Finding::query()
                    // ->when($startDate, function ($query, $startDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($startDate) {
                    //         $query->where('created_at', '>=', $startDate);
                    //     });
                    // })
                    // ->when($endDate, function ($query, $endDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($endDate) {
                    //         $query->where('created_at', '<=', $endDate);
                    //     });
                    // })
                    // ->when(
                    //     $auditStatus,
                    //     function ($query, $auditStatus) {
                    //         $query->whereHas('observation.audit', fn($query) => $query->where('status', $auditStatus));
                    //     }
                    // )
                    // ->when(
                    //     $findingType,
                    //     function ($query, $findingType) {
                    //         $query->where('type', $findingType);
                    //     }
                    // )
                    // ->when(
                    //     $unitDepartment,
                    //     function ($query, $unitDepartment) {
                    //         $query->whereHas('observation.audit', function ($query) use ($unitDepartment) {
                    //             $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment));
                    //         });
                    //     }
                    // )

                    ->sum('amount'),
                2
            ))
                ->description('Total amount')
                ->color('success'),
            Stat::make('Surcharge', Number::format(
                Finding::query()
                    // ->when($startDate, function ($query, $startDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($startDate) {
                    //         $query->where('created_at', '>=', $startDate);
                    //     });
                    // })
                    // ->when($endDate, function ($query, $endDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($endDate) {
                    //         $query->where('created_at', '<=', $endDate);
                    //     });
                    // })
                    // ->when(
                    //     $auditStatus,
                    //     function ($query, $auditStatus) {
                    //         $query->whereHas('observation.audit', fn($query) => $query->where('status', $auditStatus));
                    //     }
                    // )
                    // ->when(
                    //     $findingType,
                    //     function ($query, $findingType) {
                    //         $query->where('type', $findingType);
                    //     }
                    // )
                    // ->when(
                    //     $unitDepartment,
                    //     function ($query, $unitDepartment) {
                    //         $query->whereHas('observation.audit', function ($query) use ($unitDepartment) {
                    //             $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment));
                    //         });
                    //     }
                    // )
                    ->sum('surcharge_amount'),
            ))
                ->description('Total surcharges in Ghana Cedis')
                ->color('success')
                ->chart($recoveryStats),
            Stat::make('Recoveries', Number::format(
                Finding::query()
                    ->join('recoveries', 'findings.id', '=', 'recoveries.finding_id')
                    // ->when($startDate, function ($query, $startDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($startDate) {
                    //         $query->where('created_at', '>=', $startDate);
                    //     });
                    // })
                    // ->when($endDate, function ($query, $endDate) {
                    //     $query->whereHas('observation.audit', function ($query) use ($endDate) {
                    //         $query->where('created_at', '<=', $endDate);
                    //     });
                    // })
                    // ->when(
                    //     $auditStatus,
                    //     function ($query, $auditStatus) {
                    //         $query->whereHas('observation.audit', fn($query) => $query->where('status', $auditStatus));
                    //     }
                    // )
                    // ->when(
                    //     $findingType,
                    //     function ($query, $findingType) {
                    //         $query->where('type', $findingType);
                    //     }
                    // )
                    // ->when(
                    //     $unitDepartment,
                    //     function ($query, $unitDepartment) {
                    //         $query->whereHas('observation.audit', function ($query) use ($unitDepartment) {
                    //             $query->whereHas('reports', fn($query) => $query->whereIn('section', $unitDepartment));
                    //         });
                    //     }
                    // )
                    ->sum('recoveries.amount'),
                2
            ))
                ->description('Total recoveries in Ghana Cedis')
                ->color('success')
                ->chart($recoveryStats),

            Stat::make(
                'Resolved',
                Number::format(
                    Finding::query()->sum('amount_resolved'),
                    2
                )
            ),
            Stat::make(
                'Outstanding',
                Number::format(
                    Finding::query()->sum('outstanding'),
                    2
                )
            )
                ->description('Total ')
                ->color('success'),
            // ->chart($observationStats),
        ];
    }
}
