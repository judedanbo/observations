<?php

namespace App\Filament\Resources\AuditorGeneralReportResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FindingsRelationManager extends RelationManager
{
    protected static string $relationship = 'findings';

    protected static ?string $title = 'Selected Findings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('section_category')
                            ->label('Section Category')
                            ->options([
                                'financial' => 'Financial',
                                'compliance' => 'Compliance',
                                'performance' => 'Performance',
                                'governance' => 'Governance',
                                'general' => 'General',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('report_section_order')
                            ->label('Section Order')
                            ->numeric()
                            ->required(),

                        Forms\Components\Toggle::make('highlighted_finding')
                            ->label('Highlight this Finding')
                            ->default(false),
                    ]),

                Forms\Components\Textarea::make('report_context')
                    ->label('Additional Context for Report')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('section_category')
                    ->label('Category')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'financial' => 'success',
                        'compliance' => 'warning',
                        'performance' => 'info',
                        'governance' => 'primary',
                        'general' => 'gray',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'general')),

                Tables\Columns\TextColumn::make('report_section_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('highlighted_finding')
                    ->label('Highlighted')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS', divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make('observation.audit.institutions.name')
                    ->label('Institution(s)')
                    ->limit(20)
                    ->listWithLineBreaks()
                    ->tooltip(function ($record) {
                        return $record->observation?->audit?->institutions->pluck('name')->join(', ');
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->getLabel()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section_category')
                    ->label('Category')
                    ->options([
                        'financial' => 'Financial',
                        'compliance' => 'Compliance',
                        'performance' => 'Performance',
                        'governance' => 'Governance',
                        'general' => 'General',
                    ])
                    ->attribute('section_category'),

                Tables\Filters\TernaryFilter::make('highlighted_finding')
                    ->label('Highlighted')
                    ->attribute('highlighted_finding'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('manage_findings')
                    ->label('Add More Findings')
                    ->icon('heroicon-o-plus')
                    ->url(fn () => route(
                        'filament.admin.resources.auditor-general-reports.manage-findings',
                        ['record' => $this->ownerRecord]
                    ))
                    ->visible(fn () => $this->ownerRecord->canBeEdited()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('section_category')
                            ->label('Section Category')
                            ->options([
                                'financial' => 'Financial',
                                'compliance' => 'Compliance',
                                'performance' => 'Performance',
                                'governance' => 'Governance',
                                'general' => 'General',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('report_section_order')
                            ->label('Section Order')
                            ->numeric()
                            ->required(),

                        Forms\Components\Toggle::make('highlighted_finding')
                            ->label('Highlight this Finding'),

                        Forms\Components\Textarea::make('report_context')
                            ->label('Additional Context for Report')
                            ->rows(3),
                    ])
                    ->using(function (array $data, $record) {
                        $this->ownerRecord->findings()->updateExistingPivot($record->id, $data);

                        return $record;
                    }),

                Tables\Actions\DeleteAction::make()
                    ->using(function ($record) {
                        $this->ownerRecord->findings()->detach($record->id);
                        $this->ownerRecord->calculateTotals();
                        $this->ownerRecord->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records) {
                            $this->ownerRecord->findings()->detach($records->pluck('id'));
                            $this->ownerRecord->calculateTotals();
                            $this->ownerRecord->save();
                        }),

                    Tables\Actions\BulkAction::make('update_category')
                        ->label('Update Category')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('section_category')
                                ->label('Section Category')
                                ->options([
                                    'financial' => 'Financial',
                                    'compliance' => 'Compliance',
                                    'performance' => 'Performance',
                                    'governance' => 'Governance',
                                    'general' => 'General',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $this->ownerRecord->findings()->updateExistingPivot(
                                    $record->id,
                                    ['section_category' => $data['section_category']]
                                );
                            }
                        }),
                ]),
            ])
            // ->defaultSort('pivot.report_section_order')
            ->paginatedWhileReordering();
    }
}
