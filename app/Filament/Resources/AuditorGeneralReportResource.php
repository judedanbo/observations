<?php

namespace App\Filament\Resources;

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Filament\Resources\AuditorGeneralReportResource\Pages;
use App\Filament\Resources\AuditorGeneralReportResource\RelationManagers;
use App\Models\AuditorGeneralReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuditorGeneralReportResource extends Resource
{
    protected static ?string $model = AuditorGeneralReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $modelLabel = 'AG Report';

    protected static ?string $pluralModelLabel = 'Auditor General Reports';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('report_type')
                                    ->required()
                                    ->enum(AuditorGeneralReportTypeEnum::class)
                                    ->options(AuditorGeneralReportTypeEnum::getOptions())
                                    ->native(false),

                                Forms\Components\TextInput::make('report_year')
                                    ->required()
                                    ->numeric()
                                    ->default(now()->year)
                                    ->minValue(2000)
                                    ->maxValue(now()->year + 5),

                                Forms\Components\DatePicker::make('period_start')
                                    ->required()
                                    ->label('Period Start Date'),

                                Forms\Components\DatePicker::make('period_end')
                                    ->required()
                                    ->label('Period End Date')
                                    ->after('period_start'),

                                Forms\Components\DatePicker::make('publication_date')
                                    ->label('Publication Date'),

                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->enum(AuditorGeneralReportStatusEnum::class)
                                    ->options(AuditorGeneralReportStatusEnum::getOptions())
                                    ->default(AuditorGeneralReportStatusEnum::DRAFT)
                                    ->native(false)
                                    ->disabled(fn($context) => $context === 'create'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Forms\Components\Section::make('Report Content')
                    ->schema([
                        Forms\Components\RichEditor::make('executive_summary')
                            ->label('Executive Summary')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('methodology')
                            ->label('Methodology')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('conclusion')
                            ->label('Conclusion')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('recommendations_summary')
                            ->label('Recommendations Summary')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_findings_count')
                                    ->label('Total Findings')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                                Forms\Components\TextInput::make('total_amount_involved')
                                    ->label('Total Amount Involved (GH¢)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled(),

                                Forms\Components\TextInput::make('total_recoveries')
                                    ->label('Total Recoveries (GH¢)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled(),
                            ]),
                    ])
                    ->collapsed()
                    ->hidden(fn($context) => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                Tables\Columns\TextColumn::make('report_type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => match ($state) {
                        AuditorGeneralReportTypeEnum::ANNUAL => 'success',
                        AuditorGeneralReportTypeEnum::QUARTERLY => 'info',
                        AuditorGeneralReportTypeEnum::SPECIAL => 'warning',
                        AuditorGeneralReportTypeEnum::PERFORMANCE => 'primary',
                        AuditorGeneralReportTypeEnum::THEMATIC => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('report_year')
                    ->label('Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->color(fn($state) => $state->getColor()),

                Tables\Columns\TextColumn::make('total_findings_count')
                    ->label('Findings')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_amount_involved')
                    ->label('Amount (GH¢)')
                    ->money('GHS', divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AuditorGeneralReportStatusEnum::getOptions())
                    ->native(false),

                Tables\Filters\SelectFilter::make('report_type')
                    ->options(AuditorGeneralReportTypeEnum::getOptions())
                    ->native(false),

                Tables\Filters\SelectFilter::make('report_year')
                    ->options(fn() => collect(range(2020, now()->year + 2))
                        ->mapWithKeys(fn($year) => [$year => $year])
                        ->toArray())
                    ->native(false),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(AuditorGeneralReport $record) => $record->canBeEdited()),
                Tables\Actions\Action::make('manage_findings')
                    ->label('Manage Findings')
                    ->icon('heroicon-o-document-plus')
                    ->url(fn(AuditorGeneralReport $record) => route('filament.admin.resources.auditor-general-reports.manage-findings', $record))
                    ->visible(fn(AuditorGeneralReport $record) => $record->canBeEdited()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->can('delete', AuditorGeneralReport::class)),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FindingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditorGeneralReports::route('/'),
            'create' => Pages\CreateAuditorGeneralReport::route('/create'),
            'view' => Pages\ViewAuditorGeneralReport::route('/{record}'),
            'edit' => Pages\EditAuditorGeneralReport::route('/{record}/edit'),
            'manage-findings' => Pages\ManageFindings::route('/{record}/manage-findings'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', AuditorGeneralReportStatusEnum::DRAFT)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
