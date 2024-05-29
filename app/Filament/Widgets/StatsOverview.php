<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\Institution;
use App\Models\Observation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
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

        // dd($recoveryStats);
        return [
            Stat::make('Audits Universe', Number::format(Institution::query()->count()))
                ->description('Audit universe')
                ->color('success')
                ->chart($institutionStats),
            Stat::make('Total Audits', Number::format(Audit::query()->count()))
                ->description('Total number of audits')
                ->color('success')
                ->chart($auditStats),
            Stat::make('Observations', Number::format(Observation::query()->count()))
                ->description('Total number of observations')
                ->color('success')
                ->chart($observationStats),
            Stat::make('Recovery', Number::format(Finding::query()->sum('amount'), 2))
                ->description('Total number of observations')
                ->color('success')
                ->chart($recoveryStats),
            // Stat::make('Surcharge', Number::format(Finding::query()->sum('surcharge_amount'), 2))
            //     ->description('Total ')
            //     ->color('success')
            //     ->chart($observationStats),
        ];
    }
}
