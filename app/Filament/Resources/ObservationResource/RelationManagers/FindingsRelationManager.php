<?php

namespace App\Filament\Resources\ObservationResource\RelationManagers;

use App\Enums\AuditStatusEnum;
use App\Enums\ObservationStatusEnum;
use App\Models\Finding;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class FindingsRelationManager extends RelationManager
{
    protected static string $relationship = 'findings';

    public function isReadOnly(): bool
    {
        $status = $this->getOwnerRecord()->status->value;
        if ($status === AuditStatusEnum::ISSUED->value) {
            return true;
        }
        if ($status === AuditStatusEnum::TERMINATED->value) {
            return true;
        }
        if ($status === AuditStatusEnum::ARCHIVED->value) {
            return true;
        }
        if ($status === AuditStatusEnum::ARCHIVED->value) {
            return true;
        }
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                Finding::getForm($this->getOwnerRecord()->id)
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(fn(Finding $record): ?string => $record->description),
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
                    ->url(fn(Finding $record): string => route('filament.admin.resources.findings.view', $record->id)),
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
                                ->columnSpanFull(),
                        ])
                        ->action(function ($data, $record) {
                            $record->recoveries()->create($data);
                            $record->save();
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
