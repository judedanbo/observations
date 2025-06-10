<?php

namespace App\Filament\Resources\InstitutionResource\RelationManagers;

use App\Models\Leader;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LeadersRelationManager extends RelationManager
{
    protected static ?string $title = 'Management Staff';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected static string $relationship = 'leaders';



    public function form(Form $form): Form
    {
        return $form
            ->schema(Leader::getForm());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple()
                    ->slideOver(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
