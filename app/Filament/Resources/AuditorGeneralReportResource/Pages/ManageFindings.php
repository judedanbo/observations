<?php

namespace App\Filament\Resources\AuditorGeneralReportResource\Pages;

use App\Filament\Resources\AuditorGeneralReportResource;
use App\Models\Finding;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageFindings extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = AuditorGeneralReportResource::class;

    protected static string $view = 'filament.resources.auditor-general-report-resource.pages.manage-findings';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return 'Manage Findings - ' . $this->record->title;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Finding::query()
                    ->with(['observation.audit.institutions', 'statuses'])
                    ->whereNotIn('id', $this->record->findings->pluck('id'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('observation.audit.institutions.name')
                    ->label('Institution(s)')
                    ->listWithLineBreaks()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel()),

                Tables\Columns\TextColumn::make('classification')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel()),

                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS', divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->money('GHS', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_due')
                    ->money('GHS', divideBy: 1)
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('selected')
                    ->label('Select')
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->updateStateUsing(function (Finding $record, $state) {
                        if ($state) {
                            $this->record->findings()->attach($record->id, [
                                'report_section_order' => $this->record->findings()->count(),
                                'section_category' => 'general',
                                'highlighted_finding' => false,
                            ]);
                        } else {
                            $this->record->findings()->detach($record->id);
                        }

                        // $this->record->calculateTotals();
                        $this->record->save();

                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution')
                    ->relationship('observation.audit.institutions', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(\App\Enums\FindingTypeEnum::cases())
                        ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])),

                Tables\Filters\SelectFilter::make('classification')
                    ->options(collect(\App\Enums\FindingClassificationEnum::cases())
                        ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])),
            ])
            ->actions([
                Tables\Actions\Action::make('add_to_report')
                    ->label('Add to Report')
                    ->icon('heroicon-o-plus')
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
                            ->default('general')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('report_section_order')
                            ->label('Section Order')
                            ->numeric()
                            ->default(fn() => $this->record->findings()->count() + 1)
                            ->required(),

                        Forms\Components\Toggle::make('highlighted_finding')
                            ->label('Highlight this Finding')
                            ->default(false),

                        Forms\Components\Textarea::make('report_context')
                            ->label('Additional Context for Report')
                            ->rows(3),
                    ])
                    ->action(function (Finding $record, array $data) {
                        $this->record->findings()->attach($record->id, $data);
                        // $this->record->calculateTotals();
                        $this->record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('add_multiple')
                    ->label('Add Selected to Report')
                    ->icon('heroicon-o-plus')
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
                            ->default('general')
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('highlighted_finding')
                            ->label('Highlight these Findings')
                            ->default(false),
                    ])
                    ->action(function ($records, array $data) {
                        foreach ($records as $index => $record) {
                            $this->record->findings()->attach($record->id, array_merge($data, [
                                'report_section_order' => $this->record->findings()->count() + $index + 1,
                            ]));
                        }
                        // $this->record->calculateTotals();
                        $this->record->save();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Report')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => AuditorGeneralReportResource::getUrl('view', ['record' => $this->record])),
        ];
    }
}
