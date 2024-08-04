<?php

namespace App\Filament\Resources\ObservationResource\RelationManagers;

use App\Enums\AuditStatusEnum;
use App\Models\Recommendation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecommendationsRelationManager extends RelationManager
{
    protected static string $relationship = 'recommendations';

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
            ->schema(Recommendation::getForm($this->getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('finding.title'),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn (Recommendation $record): string|null => $record->description),
                Tables\Columns\TextColumn::make('title')
                    ->label('Recommendations')
                    ->description(fn (Recommendation $record): string|null => $record->description),
                Tables\Columns\TextColumn::make('followUps.title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ActionGroup::make([
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
