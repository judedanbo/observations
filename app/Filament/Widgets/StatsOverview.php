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
        $institutions = $this->filters['institutions'];
        $districts = $this->filters['districts'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        $department = $this->filters['department'];
        $unit = $this->filters['unit'];
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
                    ->when($institutions, function ($query, $institutions) {
                        return $query->whereIn('id', $institutions);
                    })
                    ->when($districts, function ($query, $districts) {
                        return $query->whereHas(
                            'district',
                            // fn($query) => $query->whereHas(
                            //     'reports',
                            fn($query) => $query->whereIn(
                                'districts.id',
                                $districts
                            )
                            // )
                        );
                    })
                    ->count()
            ))
                ->description('Clients')
                ->color('success')
                ->chart($institutionStats),
            Stat::make('Total Audits', Number::format(
                Audit::query()
                    ->when($institutions, fn($query, $institutions) => $query->whereHas(
                        'institutions',
                        fn($query) => $query->whereIn(
                            'institutions.id',
                            $institutions
                        )
                    ))
                    ->when(
                        $districts,
                        fn($query, $districts) => $query->whereHas(
                            'districts',
                            fn($query) => $query->whereIn(
                                'districts.id',
                                $districts
                            )
                        )
                    )
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
                    ->when($institutions, function ($query, $institutions) {
                        $query->whereHas('audit.institutions', function ($query) use ($institutions) {
                            $query->whereIn('institutions.id',  $institutions);
                        });
                    })
                    ->when($districts, function ($query, $districts) {
                        $query->whereHas('audit.districts', function ($query) use ($districts) {
                            $query->whereIn('districts.id', $districts);
                        });
                    })
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
                    ->when($institutions, function ($query, $institutions) {
                        $query->whereHas('observation.audit', function ($query) use ($institutions) {
                            $query->whereHas(
                                'institutions',
                                fn($query) => $query->whereIn(
                                    'institutions.id',
                                    $institutions
                                )
                            );
                        });
                    })
                    ->when($districts, function ($query, $districts) {
                        $query->whereHas('observation.audit', function ($query) use ($districts) {
                            $query->whereHas(
                                'districts',
                                fn($query) => $query->whereIn(
                                    'districts.id',
                                    $districts
                                )
                            );
                        });
                    })
                    ->when(
                        $auditStatus,
                        function ($query, $auditStatus) {
                            $query->whereHas(
                                'observation.audit',
                                fn($query) => $query->where('status', $auditStatus)
                            );
                        }
                    )
                    ->when(
                        $findingType,
                        function ($query, $findingType) {
                            $query->where('type', $findingType);
                        }
                    )
                    ->when(
                        $observationStatus,
                        function ($query, $observationStatus) {
                            $query->whereHas('observation', fn($query) => $query->where('status', $observationStatus));
                        }
                    )
                    ->when(
                        $department,
                        function ($query, $department) {
                            $query->whereHas('observation.audit', function ($query) use ($department) {
                                $query->whereHas(
                                    'units.department',
                                    fn($query) => $query->where(
                                        'departments.id',
                                        $department
                                    )
                                );
                            });
                        }
                    )
                    ->when(
                        $unit,
                        function ($query, $unit) {
                            $query->whereHas('observation.audit', function ($query) use ($unit) {
                                $query->whereHas(
                                    'units',
                                    fn($query) => $query->where('units.id', $unit)
                                );
                            });
                        }
                    )
                    ->sum('amount'),
                2
            ))
                ->description('Total observation amount')
                ->color('success'),

            Stat::make('Surcharge', Number::format(
                Finding::query()
                    ->when($institutions, function ($query, $institutions) {
                        $query->whereHas('observation.audit', function ($query) use ($institutions) {
                            $query->whereHas(
                                'institutions',
                                fn($query) => $query->whereIn(
                                    'institutions.id',
                                    $institutions
                                )
                            );
                        });
                    })
                    ->when($districts, function ($query, $districts) {
                        $query->whereHas('observation.audit', function ($query) use ($districts) {
                            $query->whereHas(
                                'districts',
                                fn($query) => $query->whereIn(
                                    'districts.id',
                                    $districts
                                )
                            );
                        });
                    })
                    ->when(
                        $auditStatus,
                        function ($query, $auditStatus) {
                            $query->whereHas(
                                'observation.audit',
                                fn($query) => $query->where('status', $auditStatus)
                            );
                        }
                    )
                    ->when(
                        $findingType,
                        function ($query, $findingType) {
                            $query->where('type', $findingType);
                        }
                    )
                    ->when(
                        $observationStatus,
                        function ($query, $observationStatus) {
                            $query->whereHas('observation', fn($query) => $query->where('status', $observationStatus));
                        }
                    )
                    ->when(
                        $department,
                        function ($query, $department) {
                            $query->whereHas('observation.audit', function ($query) use ($department) {
                                $query->whereHas(
                                    'units.department',
                                    fn($query) => $query->where(
                                        'departments.id',
                                        $department
                                    )
                                );
                            });
                        }
                    )
                    ->when(
                        $unit,
                        function ($query, $unit) {
                            $query->whereHas('observation.audit', function ($query) use ($unit) {
                                $query->whereHas(
                                    'units',
                                    fn($query) => $query->where('units.id', $unit)
                                );
                            });
                        }
                    )
                    ->sum('surcharge_amount'),
            ))
                ->description('Total surcharges in Ghana Cedis')
                ->color('success')
                ->chart($recoveryStats),
            Stat::make(
                'Amount Due',
                Number::format(
                    Finding::query()
                        ->sum('amount_due'),
                    2
                )
            )
                ->description('Total amount due')
                ->color('success'),
            // ->chart($observationStats),
            Stat::make('Recoveries', Number::format(
                Finding::query()
                    ->when($institutions, function ($query, $institutions) {
                        $query->whereHas('observation.audit', function ($query) use ($institutions) {
                            $query->whereHas(
                                'institutions',
                                fn($query) => $query->whereIn(
                                    'institutions.id',
                                    $institutions
                                )
                            );
                        });
                    })
                    ->when($districts, function ($query, $districts) {
                        $query->whereHas('observation.audit', function ($query) use ($districts) {
                            $query->whereHas(
                                'districts',
                                fn($query) => $query->whereIn(
                                    'districts.id',
                                    $districts
                                )
                            );
                        });
                    })
                    ->when(
                        $auditStatus,
                        function ($query, $auditStatus) {
                            $query->whereHas(
                                'observation.audit',
                                fn($query) => $query->where('status', $auditStatus)
                            );
                        }
                    )
                    ->when(
                        $findingType,
                        function ($query, $findingType) {
                            $query->where('type', $findingType);
                        }
                    )
                    ->when(
                        $observationStatus,
                        function ($query, $observationStatus) {
                            $query->whereHas('observation', fn($query) => $query->where('status', $observationStatus));
                        }
                    )
                    ->when(
                        $department,
                        function ($query, $department) {
                            $query->whereHas('observation.audit', function ($query) use ($department) {
                                $query->whereHas(
                                    'units.department',
                                    fn($query) => $query->where(
                                        'departments.id',
                                        $department
                                    )
                                );
                            });
                        }
                    )
                    ->when(
                        $unit,
                        function ($query, $unit) {
                            $query->whereHas('observation.audit', function ($query) use ($unit) {
                                $query->whereHas(
                                    'units',
                                    fn($query) => $query->where('units.id', $unit)
                                );
                            });
                        }
                    )
                    ->join('recoveries', 'findings.id', '=', 'recoveries.finding_id')
                    ->sum('recoveries.amount'),
                2
            ))
                ->description('Total recoveries in Ghana Cedis')
                ->color('success')
                ->chart($recoveryStats),

            Stat::make(
                'Resolved',
                Number::format(
                    Finding::query()
                        ->when($institutions, function ($query, $institutions) {
                            $query->whereHas('observation.audit', function ($query) use ($institutions) {
                                $query->whereHas(
                                    'institutions',
                                    fn($query) => $query->whereIn(
                                        'institutions.id',
                                        $institutions
                                    )
                                );
                            });
                        })
                        ->when($districts, function ($query, $districts) {
                            $query->whereHas('observation.audit', function ($query) use ($districts) {
                                $query->whereHas(
                                    'districts',
                                    fn($query) => $query->whereIn(
                                        'districts.id',
                                        $districts
                                    )
                                );
                            });
                        })
                        ->when(
                            $auditStatus,
                            function ($query, $auditStatus) {
                                $query->whereHas(
                                    'observation.audit',
                                    fn($query) => $query->where('status', $auditStatus)
                                );
                            }
                        )
                        ->when(
                            $findingType,
                            function ($query, $findingType) {
                                $query->where('type', $findingType);
                            }
                        )
                        ->when(
                            $observationStatus,
                            function ($query, $observationStatus) {
                                $query->whereHas('observation', fn($query) => $query->where('status', $observationStatus));
                            }
                        )
                        ->when(
                            $department,
                            function ($query, $department) {
                                $query->whereHas('observation.audit', function ($query) use ($department) {
                                    $query->whereHas(
                                        'units.department',
                                        fn($query) => $query->where(
                                            'departments.id',
                                            $department
                                        )
                                    );
                                });
                            }
                        )
                        ->when(
                            $unit,
                            function ($query, $unit) {
                                $query->whereHas('observation.audit', function ($query) use ($unit) {
                                    $query->whereHas(
                                        'units',
                                        fn($query) => $query->where('units.id', $unit)
                                    );
                                });
                            }
                        )
                        ->sum('amount_resolved'),
                    2
                )
            ),

        ];
    }
}
