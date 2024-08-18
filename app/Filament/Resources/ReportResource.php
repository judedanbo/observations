<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers\RecommendationsRelationManager;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    // protected static ?string $navigationGroup = 'GAS';

    protected static ?string $label = 'Excel Imports';

    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make('Audit')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('section'),
                            TextEntry::make('institution.name'),
                            TextEntry::make('audit.title')
                                ->columnSpanFull()
                                ->label('Audit Title'),
                            TextEntry::make('finding.observation.title')
                                ->label('Observation')
                                ->columnSpanFull(),
                            TextEntry::make('finding.title')
                                ->columnSpanFull(),
                        ]),
                ]),
                Split::make([
                    Section::make('Implementation')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('implementation_date')
                                ->date(),
                            TextEntry::make('implementation_status'),
                            TextEntry::make('comments')
                                ->columnSpanFull(),
                        ]),
                ]),
                Split::make([
                    Section::make('Observation')
                        ->columnStart(1)
                        ->columns(4)
                        ->schema([
                            TextEntry::make('paragraphs'),
                            TextEntry::make('title')
                                ->columnSpan(2),
                            TextEntry::make('type')
                                ->badge(),
                            TextEntry::make('recommendation')
                                ->columnStart(1)
                                ->columnSpanFull(),
                            TextEntry::make('amount')
                                ->numeric()
                                ->prefix('GH₵ '),
                            TextEntry::make('surcharge_amount')
                                ->numeric()
                                ->prefix('GH₵ '),
                            TextEntry::make('amount_recovered')
                                ->numeric()
                                ->prefix('GH₵ ')
                                ->columnStart(4),
                            // TextEntry::make('implementation_date')
                            //     ->date(),
                            // TextEntry::make('implementation_status'),
                            // TextEntry::make('comments'),
                        ]),
                ])
                    ->columnStart(1)
                    ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('section'),
                Tables\Columns\TextColumn::make('institution.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('audit.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paragraphs')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_recovered')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('implementation_date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('implementation_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
