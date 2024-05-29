<?php

namespace App\Filament\Widgets;

use App\Models\Audit;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AuditsTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(Audit::query())
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
                Tables\Columns\ColumnGroup::make('Planned')
                    ->alignment(Alignment::Center)
                    ->columns([
                        Tables\Columns\TextColumn::make('planned_start_date')
                            ->label('Start Date')
                            ->date(),
                        Tables\Columns\TextColumn::make('planned_end_date')
                            ->label('End Date')
                            ->date(),
                    ]),
                Tables\Columns\ColumnGroup::make('Actual')
                    ->alignment(Alignment::Center)
                    ->columns([
                        Tables\Columns\TextColumn::make('actual_start_date')
                            ->label('Start Date')
                            ->date(),
                        Tables\Columns\TextColumn::make('actual_end_date')
                            ->label('End Date')
                            ->date(),
                    ]),
                Tables\Columns\TextColumn::make('year')
                    ->label('Audit Year')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('observations_count')
                    ->label('Observations')
                    ->counts('observations')
                    ->alignRight()
                    ->numeric(),

                Tables\Columns\TextColumn::make('findings_count')
                    ->label('Total Findings')
                    ->counts('findings')
                    ->alignRight()
                    ->numeric(),
            ]);
    }
}
