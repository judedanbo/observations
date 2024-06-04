<?php

namespace App\Filament\Widgets;

use App\Models\Institution;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class AuditUniverseTable extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = [
        'md' => 3,
        // 'xl' => 3,
    ];
    public function table(Table $table): Table
    {
        $unitDepartment = $this->filters['unit_department'];
        $observationStatus = $this->filters['observation_status'];

        return $table
            ->query(
                Institution::query()
                    ->when($unitDepartment, function ($query, $unitDepartment) {
                        return $query->whereHas('audits', fn ($query) => $query->whereHas('reports', fn ($query) => $query->whereIn('section', $unitDepartment)));
                    })
                    ->when($observationStatus, function ($query, $observationStatus) {
                        return $query->whereHas('audits', fn ($query) => $query->whereHas('observations', fn ($query) => $query->where('status', $observationStatus)));
                    })
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('leaders.name')
                    ->label('Leadership')
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
