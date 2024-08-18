<?php

namespace App\Filament\Resources;

use App\Enums\ObservationStatusEnum;
use App\Filament\Resources\ObservationResource\Pages;
use App\Filament\Resources\ObservationResource\RelationManagers\ActionsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\FindingsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\RecommendationsRelationManager;
use App\Models\Observation;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationGroup = 'Audit';

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Observation::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cause.title')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable(),
                Tables\Columns\TextColumn::make('effect.title')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable(),
                Tables\Columns\TextColumn::make('findings.title')
                    ->label('Findings')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->bulleted()
                    ->expandableLimitedList()
                    // ->counts('findings')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recommendations.title')
                    ->label('Recommendations')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('audit.title')
                //     // ->description(fn ($record) => $record->criteria)
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('actions.title')
                //     // ->badge()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('followUps.title')
                    // ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions(
                ActionGroup::make(
                    [
                        Tables\Actions\Action::make('sent for review')
                            ->icon('heroicon-o-paper-airplane')
                            ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::DRAFT)
                            ->label('Send for review')
                            ->action(fn (Observation $record) => $record->review()),

                        Tables\Actions\Action::make('issue')
                            ->icon('heroicon-o-paper-airplane')
                            ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::IN_REVIEW)
                            ->label('Issue Observation')
                            ->action(fn (Observation $record) => $record->issue()),
                        // Tables\Actions\Action::make('received')
                        //     ->icon('heroicon-o-paper-airplane')
                        //     ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::IN_REVIEW)
                        //     ->label('Issue Observation')
                        //     ->action(fn (Observation $record) => $record->issue()),
                        Tables\Actions\EditAction::make()
                            ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::DRAFT),
                        Tables\Actions\ViewAction::make(),
                    ]
                )
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FindingsRelationManager::class,
            RecommendationsRelationManager::class,
            ActionsRelationManager::class,
            FollowUpsRelationManager::class,
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    Split::make([
                        Section::make('Audit Information')
                            ->heading('Audit Information (Audit Title, Observation Title, Status)')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('audit.title')
                                    ->label('Audit Title')
                                    ->columnSpan(2),
                                TextEntry::make('status')
                                    ->label('')
                                    ->badge()
                                    ->alignRight(),
                            ]),
                    ]),
                    Split::make([
                        Section::make('Observation Information')
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Observation Title')
                                    ->columnSpanFull(),
                                TextEntry::make('criteria')
                                    ->html()
                                    ->columnSpanFull(),
                            ]),
                    ]),
                ]
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObservations::route('/'),
            // 'create' => Pages\CreateObservation::route('/create'),
            // 'edit' => Pages\EditObservation::route('/{record}/edit'),
            'view' => Pages\ViewObservation::route('/{record}'),
        ];
    }
}
