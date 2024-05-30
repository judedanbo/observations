<?php

namespace App\Filament\Resources\ObservationResource\RelationManagers;

use App\Models\Finding;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class FindingsRelationManager extends RelationManager
{
    protected static string $relationship = 'findings';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Finding::getForm($this->getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(fn (Finding $record): string  | null =>  $record->description),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('causes.title'),
                Tables\Columns\TextColumn::make('effects.title'),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->alignRight(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Add Cause')
                        ->icon('heroicon-o-link'),
                    Action::make('Add Effect')
                        ->icon('heroicon-o-megaphone'),
                    Action::make('Surcharge')
                        ->icon('heroicon-o-banknotes'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
