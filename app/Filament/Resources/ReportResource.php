<?php

namespace App\Filament\Resources;

use App\Enums\AuditTypeEnum;
use App\Enums\FindingClassificationEnum;
use App\Enums\FindingTypeEnum;
use App\Filament\Exports\ReportExporter;
use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers\ActionsRelationManager;
use App\Filament\Resources\ReportResource\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\ReportResource\RelationManagers\RecommendationsRelationManager;
use App\Models\Document;
use App\Models\FollowUp;
use App\Models\Report;
use App\Models\Surcharge;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Runner\Baseline\Issue;

class ReportResource extends Resource
{
    // protected static ?string $navigationGroup = 'GAS';

    protected static ?string $label = 'Upload Issues';

    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    // protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Split::make([
                Section::make('Audit')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('section')
                            ->label('Audit report type'),
                        TextEntry::make('institution.name'),
                        TextEntry::make('audit.title')
                            ->columnSpanFull()
                            ->label('Audit Title'),
                        // TextEntry::make('finding.title')
                        //     // ->size('lg')
                        //     ->columnSpanFull(),
                        // TextEntry::make('finding.observation.title')
                        //     ->label(false)
                        //     ->columnSpanFull(),
                    ]),
                // ]),
                Split::make([
                    Section::make('Observation')
                        ->collapsible()
                        ->columnStart(1)
                        ->columns(4)
                        ->schema([
                            TextEntry::make('paragraphs'),
                            TextEntry::make('finding.title')
                                ->label('Finding title')
                                ->columnSpan(2),
                            TextEntry::make('finding.type')
                                ->label('Finding type')
                                ->badge(),
                            TextEntry::make('finding.recommendations.title')
                                ->label('Recommendations')
                                ->columnStart(1)
                                ->columnSpanFull(),
                            TextEntry::make('finding.amount')
                                ->label('Finding Amount')
                                ->numeric(),
                            TextEntry::make('finding.surcharge_amount')
                                ->label('Surcharge Amount')
                                ->numeric(),
                            TextEntry::make('finding.total_recoveries')
                                ->label('Amount Recovered')
                                ->prefix('GH¢ ')
                                // ->money('GHS', locale: 'gh')
                                ->numeric()
                                ->columnStart(4),
                            TextEntry::make('finding.documents.title')
                                ->visible(fn(Report $record) => $record->finding->documents->count() > 0)
                                ->label('Documents')
                                // ->html()
                                ->listWithLineBreaks()
                                ->limitList(2)
                                ->expandableLimitedList()

                            // TextEntry::make('implementation_date')
                            //     ->date(),
                            // TextEntry::make('implementation_status'),
                            // TextEntry::make('comments'),
                        ]),
                ])
                    ->columnStart(1)
                    ->columnSpanFull(),
                // Split::make([
                Section::make('Classification')
                    ->label('Issue classification/Resolution')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('finding.classification')
                            ->label('Classification')
                            ->badge(),
                        TextEntry::make('finding.amount_resolved')
                            ->label('Amount Resolved')
                            ->numeric(),
                    ]),
                Section::make('Implementation')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('implementation_date')
                            ->date(),
                        TextEntry::make('implementation_status'),
                        TextEntry::make('comments')
                            ->columnSpanFull(),
                    ]),
                // ]),


            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Report::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters issues');
            })
            ->filtersFormColumns(3)
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->label('Audit report type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('audit.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paragraphs')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.title')
                    ->label('Finding title')
                    ->icon(function (Report $record) {
                        if ($record->finding->documents->count() > 0) {
                            return 'heroicon-o-paper-clip';
                        }
                        // return $record->finding->icon();
                    })
                    ->iconPosition(IconPosition::After)
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.type')
                    ->searchable()
                    ->label('Finding type')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.classification')
                    ->searchable()
                    ->label('Classification')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.amount')
                    ->searchable()
                    ->label('Amount')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finding.total_recoveries')
                    // ->sum('finding', 'total_recoveries')
                    // ->searchable()
                    ->label('Total Recovered')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finding.amount_resolved')
                    ->label('Amount Resolved')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finding.surcharge_amount')
                    ->searchable()
                    ->label('Surcharge Amount')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finding.statuses.name')
                    ->label('Implementation Status')
                    ->listWithLineBreaks()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.statuses.implementation_date')
                    ->date()
                    ->label('Implementation Date')
                    ->listWithLineBreaks()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(
                [
                    SelectFilter::make('Region')
                        ->native()
                        ->searchable()
                        ->preload()
                        ->relationship('region', 'name')
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['value'], function ($query, $data) {
                                $query->whereHas('audit', function ($query) use ($data) {
                                    $query->whereHas('district', function ($query) use ($data) {
                                        $query->whereHas('region', function ($query) use ($data) {
                                            $query->where('regions.id', $data);
                                        });
                                        // $query->where('name', $data);
                                    });
                                });
                            });
                        }),
                    SelectFilter::make('District')
                        ->native()
                        ->searchable()
                        ->preload()
                        ->relationship('district', 'name')
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['value'], function ($query, $data) {
                                $query->whereHas('audit', function ($query) use ($data) {
                                    $query->whereHas('districts', function ($query) use ($data) {
                                        $query->where('districts.id', $data);
                                    });
                                });
                            });
                        }),
                    SelectFilter::make('Office')
                        ->relationship('audit.offices', 'name')
                        ->multiple()
                        ->native()
                        ->searchable()
                        ->preload()
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['values'], function ($query, $data) {
                                $query->whereHas('audit', function ($query) use ($data) {
                                    $query->whereHas('offices', function ($query) use ($data) {
                                        $query->where('offices.id', $data);
                                    });
                                });
                            });
                        }),
                    SelectFilter::make('section')
                        ->options(AuditTypeEnum::class),
                    SelectFilter::make('Audit report title')
                        ->relationship('audit', 'title')
                        ->label('Audit report title'),
                    SelectFilter::make('institution_id')
                        ->relationship('institution', 'name')
                        ->label('Institution'),
                    SelectFilter::make('type')
                        ->label('Finding type')
                        ->options(FindingTypeEnum::class),
                    // SelectFilter::make('region')
                    //     ->relationship('finding', 'region')
                    //     ->label('Region')
                    //     ->options(FindingTypeEnum::class),
                    SelectFilter::make('classification')
                        ->label('Issue classification')
                        // ->relationship('finding', 'classification')
                        ->options(FindingClassificationEnum::class)
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['value'], function ($query, $data) {
                                // dd($data);
                                $query->whereHas('finding', function ($query) use ($data) {
                                    $query->where('classification', $data);
                                });
                            });
                        }),
                ],
                // layout: FiltersLayout::AboveContentCollapsible
            )
            ->headerActions(
                [
                    Tables\Actions\ExportAction::make()
                        ->icon('heroicon-o-arrow-down-tray')
                        ->label('Export All reports')
                        ->exporter(ReportExporter::class)
                        // ->columnMapping(false)
                        ->fileName('reports'),
                ],

            )
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Tables\Actions\Action::make('add_document')
                        ->label('Add supporting Document')
                        ->icon('heroicon-o-document-plus')
                        ->form(Document::getForm())
                        ->mutateFormDataUsing(function (Model $record, array $data): array {
                            // dd($record);
                            $data['report_id'] = $record->id;
                            return $data;
                        })
                        ->action(fn(Report $record, array $data) => $record->addDocuments($data)),
                    Tables\Actions\Action::make('classification')
                        ->label('Add Classification')
                        ->icon('heroicon-o-rectangle-group')
                        ->form([
                            Select::make('classification')
                                ->enum(FindingClassificationEnum::class)
                                ->options(FindingClassificationEnum::class)
                                ->label('Surcharge Amount')
                                ->placeholder('Please select classification')
                                ->required(),
                        ])
                        ->action(function ($data, $record) {
                            $newAmount = $record->finding()->update([
                                'classification' => $data['classification']
                            ]);
                            Notification::make('Classification Added')
                                ->title('Classification Added')
                                ->body('The classification has been added successfully.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('amount_resolved')
                        ->label('Add amount resolved')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            TextInput::make('amount_resolved')
                                ->label('Amount resolved')
                                ->placeholder('Enter amount resolved')
                                ->required(),
                        ])
                        ->action(function ($data, $record) {
                            $newAmount = $record->finding()->update([
                                'amount_resolved' => $data['amount_resolved']
                            ]);
                            Notification::make('Amount resolved added')
                                ->title('Amount Resolved added')
                                ->body('The amount resolved has been added successfully.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('Surcharge')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            TextInput::make('surcharge_amount')
                                ->label('Surcharge Amount')
                                ->placeholder('Enter Surcharge Amount')
                                ->required(),
                        ])
                        ->action(function ($data, $record) {
                            $newAmount = $record->finding()->update([
                                'surcharge_amount' => $data['surcharge_amount']
                            ]);
                            // dd($record->finding);
                            // $record->save();
                            Notification::make('Surcharge Added')
                                ->title('Surcharge Added')
                                ->body('The surcharge has been added successfully.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('Record recovery')
                        ->icon('heroicon-o-banknotes')
                        ->form([
                            TextInput::make('amount')
                                ->type('number')
                                ->label('Amount recovered')
                                ->minValue(0)
                                ->step(0.01)
                                ->required(),
                            TextInput::make('comments')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->action(function ($data, $record) {
                            $finding = $record->finding->recoveries()->create($data);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->icon('heroicon-o-document-arrow-down')
                    ->label('Export selected rows')
                    ->fileName('reports')
                    ->exporter(ReportExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Client Action', [
                ActionsRelationManager::class,
            ]),
            RelationGroup::make('Finding follow up', [
                FollowUpsRelationManager::class,
            ]),
            RelationGroup::make('PAC Recommendations', [
                RecommendationsRelationManager::class,
            ]),

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            // 'create' => Pages\CreateReport::route('/create'),
            // 'edit' => Pages\EditReport::route('/{record}/edit'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
