<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ReportExporter;
use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers\RecommendationsRelationManager;
use App\Models\Document;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use PHPUnit\Runner\Baseline\Issue;

class ReportResource extends Resource
{
    // protected static ?string $navigationGroup = 'GAS';

    protected static ?string $label = 'Upload Issues';

    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

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
                        ->columnStart(1)
                        ->columns(4)
                        ->schema([
                            TextEntry::make('paragraphs'),
                            TextEntry::make('finding.title')
                                ->columnSpan(2),
                            TextEntry::make('finding.type')
                                ->badge(),
                            TextEntry::make('finding.recommendations.title')
                                ->columnStart(1)
                                ->columnSpanFull(),
                            TextEntry::make('finding.amount')
                                ->numeric()
                                ->prefix('GH₵ '),
                            TextEntry::make('finding.surcharge_amount')
                                ->numeric()
                                ->prefix('GH₵ '),
                            TextEntry::make('findings.amount_recovered')
                                ->numeric()
                                ->prefix('GH₵ ')
                                ->columnStart(4),
                            TextEntry::make('finding.documents.title')
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
            ->columns([
                Tables\Columns\TextColumn::make('section')
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_recovered')
                    ->searchable()
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finding.statuses.implementation_date')
                    ->date()
                    ->listWithLineBreaks()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.statuses.name')
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Export All reports')
                    ->exporter(ReportExporter::class)
                    // ->columnMapping(false)
                    ->fileName('reports'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Tables\Actions\Action::make('add_document')
                        ->label('Add supporting Document')
                        ->icon('heroicon-o-document-plus')
                        ->form(Document::getForm())
                        ->mutateFormDataUsing(function (array $data): array {
                            $data['report_id'] = $this->getOwnerRecord()->id;
                            return $data;
                        })
                        ->action(fn(Report $record, array $data) => $record->addDocuments($data)),
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
            RecommendationsRelationManager::class,
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
