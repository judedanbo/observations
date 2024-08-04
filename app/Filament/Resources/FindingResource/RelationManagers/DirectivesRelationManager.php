<?php

namespace App\Filament\Resources\FindingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DirectivesRelationManager extends RelationManager
{
    protected static string $relationship = 'directives';

    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('pac_directive')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                TextInput::make('client_responsible_officer'),
                TextInput::make('gas_assigned_officer')
                    ->columnStart(1),
                DatePicker::make('pac_directive_date')
                    // ->columnStart(1)
                    ->native(false)
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pac_directive')
            ->columns([
                Tables\Columns\TextColumn::make('pac_directive'),
                Tables\Columns\TextColumn::make('client_responsible_officer'),
                Tables\Columns\TextColumn::make('gas_assigned_officer'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
