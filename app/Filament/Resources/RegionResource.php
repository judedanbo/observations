<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\RelationManagers\OfficesRelationManager;
use App\Filament\Resources\RegionResource\Pages;
use App\Filament\Resources\RegionResource\RelationManagers\DistrictsRelationManager;
use App\Models\Region;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationGroup = 'GAS';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Region::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('districts_count')
                    ->counts('districts')
                    ->searchable(),
                Tables\Columns\TextColumn::make('offices_count')
                    ->counts('offices')
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
                Tables\Actions\EditAction::make(),
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
            DistrictsRelationManager::class,
            OfficesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
            // 'create' => Pages\CreateRegion::route('/create'),
            // 'edit' => Pages\EditRegion::route('/{record}/edit'),
            'view' => Pages\ViewRegion::route('/{record}'),
        ];
    }
}
