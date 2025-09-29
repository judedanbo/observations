<?php

namespace App\Filament\Widgets;

use App\Models\Institution;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;

class AuditUniverseTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 8;

    protected static ?string $heading = 'Client list';

    protected int|string|array $columnSpan = [
        'md' => 3,
        // 'xl' => 3,
    ];

    public function table(Table $table): Table
    {
        // $unitDepartment = $this->filters['unit_department'];
        $districts = $this->filters['districts'];
        $institutions = $this->filters['institutions'];
        $observationStatus = $this->filters['observation_status'];

        return $table
            ->query(
                Institution::query()
                    // ->when($unitDepartment, function ($query, $unitDepartment) {
                    //     return $query->whereHas('audits', fn($query) => $query->whereHas('reports', fn($query) => $query->where('section', $unitDepartment)));
                    // })
                    ->when(
                        $institutions,
                        function ($query, $institutions) {
                            // return $query->whereHas('institutions', function ($query) use ($institutions) {
                            $query->whereIn('id', $institutions);
                            // });
                        }
                        // fn($query, $institutions) => $query->whereHas('regions', fn($query) => $query->where('id', 'in', $institutions))
                    )
                    ->when($districts, fn ($query, $districts) => $query->whereHas(
                        'district',
                        fn ($query) => $query->whereIn('districts.id', $districts)
                    ))
                    ->when($observationStatus, function ($query, $observationStatus) {
                        return $query->whereHas('audits', fn ($query) => $query->whereHas('observations', fn ($query) => $query->where('status', $observationStatus)));
                    })
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('leaders.name')
                    ->label('Key Staff')
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->searchable(),
                // Tables\Columns\TextColumn::make('district.name'),
                Tables\Columns\TextColumn::make('reports_count')
                    ->label('Reports')
                    ->counts('reports')
                    ->numeric()
                    ->alignRight(),
            ])
            ->recordUrl(fn (Institution $record): string => route('filament.admin.resources.institutions.view', $record));
        // ->actions([
        //     Tables\Actions\ViewAction::make()
        //         ->url(fn (Institution $record): string => route('filament.admin.resources.institutions.view', $record)),
        // ]);
    }
}
