<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FindingResource\Pages;
use App\Filament\Resources\FindingResource\RelationManagers\CausesRelationManager;
use App\Filament\Resources\FindingResource\RelationManagers\DirectivesRelationManager;
use App\Filament\Resources\FindingResource\RelationManagers\EffectsRelationManager;
use App\Filament\Resources\FindingResource\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\FindingResource\RelationManagers\RecommendationsRelationManager;
use App\Filament\Resources\FindingResource\RelationManagers\RecoveriesRelationManager;
use App\Models\Finding;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FindingResource extends Resource
{
    protected static ?string $model = Finding::class;

    protected static ?string $navigationGroup = 'Audit';

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Finding::getForm());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Audit / Observation Information')
                    ->collapsible()
                    ->columns(['md' => 3])
                    ->schema([
                        TextEntry::make('observation.status')
                            ->label('Observation status')
                            ->badge(),
                        TextEntry::make('observation.audit.title')
                            ->label('Audit title'),
                        TextEntry::make('observation.title'),
                    ]),
                Section::make('Finding Information')
                    ->collapsible()
                    ->columns(['md' => 4])
                    ->schema([
                        TextEntry::make('type')
                            ->label('')
                            ->badge(),
                        TextEntry::make('title')
                            ->label('Finding title')
                            ->columnSpan(3),
                        TextEntry::make('description')
                            ->label('Finding description')
                            ->columnSpanFull(),
                        TextEntry::make('amount')
                            ->badge(),
                        TextEntry::make('surcharge')
                            ->badge(),
                        TextEntry::make('recoveriesSum'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('observation.audit.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observation.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Finding')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Finding Type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->alignRight()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->numeric()
                    ->alignRight()
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
            CausesRelationManager::class,
            EffectsRelationManager::class,
            RecommendationsRelationManager::class,
            FollowUpsRelationManager::class,
            RecoveriesRelationManager::class,
            DirectivesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFindings::route('/'),
            // 'create' => Pages\CreateFinding::route('/create'),
            // 'edit' => Pages\EditFinding::route('/{record}/edit'),
            'view' => Pages\ViewFinding::route('/{record}'),
        ];
    }
}
