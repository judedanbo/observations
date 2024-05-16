<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservationResource\Pages;
use App\Filament\Resources\ObservationResource\RelationManagers\ActionsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\AuditActionsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\FindingsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\ObservationResource\RelationManagers\RecommendationsRelationManager;
use App\Models\Observation;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationGroup = 'Audit';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        Tables\Actions\EditAction::make(),
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
            // ActionsRelationManager::class,
            FollowUpsRelationManager::class
        ];
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    Section::make('Audit Information')
                        ->schema([
                            TextEntry::make('audit.title')
                                ->label('Audit Title'),
                        ]),
                    Section::make('Observation Information')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('title')
                                ->label('Observation Title'),
                            TextEntry::make('status')
                                ->label('')
                                ->badge()
                                ->alignRight(),

                            TextEntry::make('causes.title')
                                ->label('Cause'),
                            TextEntry::make('effects.title')
                                ->label('Effect'),
                            // TextEntry::make('findings.title')
                            //     ->label('Recommendation'),
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
