<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurchargeResource\Pages;
use App\Models\Surcharge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SurchargeResource extends Resource
{
    protected static ?string $model = Surcharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                Tables\Actions\Action::make('surcharge')
                    ->form([
                        Forms\Components\TextInput::make('surcharge_amount')
                            ->label('surcharge_amount')
                            ->required(),
                    ])
                    ->action(function ($data, $record) {
                        $record->surcharge([
                            $data['surcharge_amount'],
                        ]);
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurcharges::route('/'),
            // 'create' => Pages\CreateSurcharge::route('/create'),
            // 'edit' => Pages\EditSurcharge::route('/{record}/edit'),
        ];
    }
}
