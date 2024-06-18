<?php

namespace App\Filament\Resources\ObservationResource\RelationManagers;

use App\Models\Finding;
use App\Models\Recovery;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
            ->schema(
                [
                    Repeater::make('findings')
                        ->schema(
                            Finding::getForm($this->getOwnerRecord()->id)
                        ),
                ]
            );
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
                Tables\Columns\TextColumn::make('causes.title')
                    ->bulleted(),
                Tables\Columns\TextColumn::make('effects.title')
                    ->bulleted(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('recoveries_sum')
                    ->label('Amount Recovered')
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

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('View')
                    ->url(fn (Finding $record): string => route('filament.admin.resources.findings.view', $record->id)),
                ActionGroup::make([
                    Tables\Actions\Action::make('Add cause')
                        ->icon('heroicon-o-link')
                        ->form([
                            TextInput::make('title')
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(250),
                            RichEditor::make('description')
                                ->columnSpanFull(),
                        ])
                        ->action(function ($data, $record) {
                            $record->causes()->create($data);
                            $record->save();
                        }),
                    Tables\Actions\Action::make('Add effect')
                        ->icon('heroicon-o-megaphone')
                        ->form([
                            TextInput::make('title')
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(250),
                            RichEditor::make('description')
                                ->columnSpanFull(),
                        ])
                        ->action(function ($data, $record) {
                            $record->effects()->create($data);
                            $record->save();
                        }),

                    Tables\Actions\Action::make('Surcharge')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            TextInput::make('surcharge_amount')
                                ->label('Surcharge Amount')
                                ->placeholder('Enter Surcharge Amount')
                                ->required(),
                        ])
                        ->action(function ($data, $record) {
                            $record->surcharge_amount = $data['surcharge_amount'];
                            $record->save();
                            Notification::make('Surcharge Added')
                                ->title('Surcharge Added')
                                ->body('The surcharge has been added successfully.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('Record recovery')
                        ->icon('heroicon-o-banknotes')
                        ->form([
                            TextInput::make('amount')
                                ->type('number')
                                ->label('Amount recovered')
                                ->minValue(0)
                                ->step(0.01)
                                ->required(),
                            TextInput::make('comments')
                                ->columnSpanFull()
                        ])
                        ->action(function ($data, $record) {
                            $record->recoveries()->create($data);
                            $record->save();
                        }),
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
