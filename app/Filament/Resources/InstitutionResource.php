<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionResource\Pages;
use App\Filament\Resources\InstitutionResource\RelationManagers\LeadersRelationManager;
use App\Models\Institution;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationGroup = 'GAS';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Institution::getForm());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns([
                'md' => 2,
                'xl' => 3,
            ])
            ->schema([
                Split::make([
                    Section::make('Institution details')
                        ->schema([
                            TextEntry::make('name'),
                        ]),
                ]),
                Split::make([
                    Section::make('Current Address')
                        ->schema([
                            TextEntry::make('current_address.street')
                                ->label(''),
                            TextEntry::make('current_address.city')
                                ->label(''),
                            TextEntry::make('current_address.region')
                                ->label(''),
                            TextEntry::make('current_address.country')
                                ->label(''),

                        ]),
                ]),
                Split::make([
                    Section::make('Location')
                        ->schema([
                            TextEntry::make('district.region.name')
                                ->label('Region'),
                            TextEntry::make('district.name')
                                ->label('District'),

                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            LeadersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            // 'create' => Pages\CreateInstitution::route('/create'),
            // 'edit' => Pages\EditInstitution::route('/{record}/edit'),
            'view' => Pages\ViewInstitution::route('/{record}'),
        ];
    }
}
