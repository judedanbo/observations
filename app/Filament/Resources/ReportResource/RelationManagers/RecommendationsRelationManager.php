<?php

namespace App\Filament\Resources\ReportResource\RelationManagers;

use App\Models\Parliament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecommendationsRelationManager extends RelationManager
{
    protected static string $relationship = 'recommendations';

    protected static ?string $label = 'PAC Recommendations';



    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Parliament::getForm($this->getOwnerRecord()->finding_id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pac_directive')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('pac_directive')
                    ->label('PAC Directive'),
                Tables\Columns\TextColumn::make('pac_directive_date')
                    ->date(),
                Tables\Columns\TextColumn::make('client_responsible_officer'),
                Tables\Columns\TextColumn::make('gas_assigned_officer'),
                Tables\Columns\TextColumn::make('implementation_date')
                    ->date(),
                Tables\Columns\TextColumn::make('completed_by_date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add PAC Recommendation')
                    ->mutateFormDataUsing(function ($data) {
                        $data['finding_id'] = $this->getOwnerRecord()->finding_id;

                        return $data;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('complete')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Parliament $parliament) => $parliament->markAsCompleted()),
                    // ->confirm('Are you sure you want to mark this recommendation as completed?')
                    // ->message('Recommendation marked as completed.'),
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
