<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class AuditsTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        // 'sm' => 6,
        'md' => 4,
        'lg' => 3,
        'xl' => 4,
    ];

    public function table(Table $table): Table
    {
        $institutions = $this->filters['institutions'];
        $districts = $this->filters['districts'];
        $auditStatus = $this->filters['audit_status'];
        $findingType = $this->filters['finding_type'];
        // $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        return $table
            ->recordUrl(fn (Audit $record): string => route('filament.admin.resources.audits.view', $record))
            ->query(
                Audit::query()
                    ->when(
                        $institutions,
                        function ($query, $institutions) {
                            return $query->whereHas('institutions', function ($query) use ($institutions) {
                                $query->whereIn('id', $institutions);
                            });
                        }
                        // fn($query, $institutions) => $query->whereHas('regions', fn($query) => $query->where('id', 'in', $institutions))
                    )
                    ->when($districts, fn ($query, $districts) => $query->whereHas(
                        'districts',
                        fn ($query) => $query->whereIn('districts.id', $districts)
                    ))
                    // ->when($endDate, fn($query, $endDate) => $query->where('created_at', '<=', $endDate))
                    ->when($auditStatus, fn ($query, $auditStatus) => $query->where('status', $auditStatus))
                    ->when($observationStatus, fn ($query, $observationStatus) => $query->whereHas('observations', fn ($query) => $query->where('status', $observationStatus)))
                    ->when($findingType, function ($query, $findingType) {
                        return $query->whereHas('findings', fn ($query) => $query->where('type', $findingType));
                    })
                // ->when($unitDepartment, function ($query, $unitDepartment) {
                //     return $query->whereHas('reports', fn($query) => $query->where('section', $unitDepartment));
                // })
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
