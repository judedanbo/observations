<?php

namespace App\Filament\Resources\AuditResource\RelationManagers;

use App\Enums\AuditDepartmentEnum;
use App\Enums\AuditStatusEnum;
use App\Models\Audit;
use App\Models\Observation;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'observations';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true; // $ownerRecord->status === Status::Draft;
    }

    public function isReadOnly(): bool
    {
        return $this->getOwnerRecord()->status !== AuditStatusEnum::IN_PROGRESS;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Observation::getForm($this->getOwnerRecord()->id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('findings_count')
                    ->label('Total Findings')
                    ->counts('findings')
                    ->alignRight()
                    ->numeric(),
                Tables\Columns\TextColumn::make('recommendations_count')
                    ->label('Recommendations')
                    ->counts('recommendations')
                    ->alignRight()
                    ->numeric(),
                // Tables\Columns\TextColumn::make('actions')
                //     ->label('Auditee Action')
                //     // ->counts('id')
                //     ->alignRight()
                //     ->numeric(),
                Tables\Columns\TextColumn::make('followUps.description')
                    ->label('Audit Follow up')
                    ->alignRight()
                    ->numeric(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->outlined()
                    ->successRedirectUrl(fn (Observation $record) => route('filament.admin.resources.observations.view', $record))
                    ->successNotification(
                        function () {
                            Notification::make('Observation Created')
                                ->title('Observation Created')
                                ->body('The observation has been created successfully.')
                                ->success();
                        }
                    ),
                Action::make('Load Observations')
                    ->outlined()
                    ->icon('heroicon-o-document-text')
                    ->visible(fn () => $this->getOwnerRecord()->status === AuditStatusEnum::IN_PROGRESS)
                    ->form([
                        Select::make('audit_type')
                            ->enum(AuditDepartmentEnum::class)
                            ->options(AuditDepartmentEnum::class)
                            ->native(false)
                            ->label('Select Audit Type'),
                        FileUpload::make('filename')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = public_path('storage/'.$data['filename']);
                        $audit = Audit::find($this->getOwnerRecord()->id);
                        $audit->importObservations($file);
                    })
                    ->after(function () {
                        Notification::make('Observations Loaded')
                            ->title('Observations Loaded')
                            ->body('Observations have been loaded successfully.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Observation $record): string => route('filament.admin.resources.observations.view', $record)),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
                // Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function getPages(): array
    {
        return [
            // 'view' => Pages\::route('/{record}'),
        ];
    }
}
