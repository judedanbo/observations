<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class AuditsTable extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = ['md' => 4,];

    public function table(Table $table): Table
    {
        $startDate = $this->filters['start_date'];
        $endDate = $this->filters['end_date'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        return $table
            ->query(
                Audit::query()
                    ->when($startDate, fn ($query, $startDate) => $query->where('created_at', '>=', $startDate))
                    ->when($endDate, fn ($query, $endDate) => $query->where('created_at', '<=', $endDate))
                    ->when($auditStatus, fn ($query, $auditStatus) => $query->where('status', $auditStatus))
                    ->when($observationStatus, fn ($query, $observationStatus) => $query->whereHas('observations', fn ($query) => $query->where('status', $observationStatus)))
                    ->when($findingType, function ($query, $findingType) {
                        return $query->whereHas('findings', fn ($query) => $query->where('type', $findingType));
                    })
                    ->when($unitDepartment, function ($query, $unitDepartment) {
                        return $query->whereHas('reports', fn ($query) => $query->whereIn('section', $unitDepartment));
                    })
            )
            // ->defaultGroup('status')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('institutions.name')
                    ->label('Institution')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable(),
                // ->bulleted(),
                // ->searchable(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                // Tables\Columns\ColumnGroup::make('Planned')
                //     ->alignment(Alignment::Center)
                //     ->columns([
                //         Tables\Columns\TextColumn::make('planned_start_date')
                //             ->label('Start Date')
                //             ->date(),
                //         Tables\Columns\TextColumn::make('planned_end_date')
                //             ->label('End Date')
                //             ->date(),
                //     ]),
                // Tables\Columns\ColumnGroup::make('Actual')
                //     ->alignment(Alignment::Center)
                //     ->columns([
                //         Tables\Columns\TextColumn::make('actual_start_date')
                //             ->label('Start Date')
                //             ->date(),
                //         Tables\Columns\TextColumn::make('actual_end_date')
                //             ->label('End Date')
                //             ->date(),
                //     ]),
                Tables\Columns\TextColumn::make('year')
                    ->label('Audit Year')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('observations_count')
                    ->label('Observations')
                    ->counts('observations')
                    ->alignRight()
                    ->numeric(),

                Tables\Columns\TextColumn::make('findings_count')
                    ->label('Findings')
                    ->counts('findings')
                    ->alignRight()
                    ->numeric(),
            ]);
    }
}
