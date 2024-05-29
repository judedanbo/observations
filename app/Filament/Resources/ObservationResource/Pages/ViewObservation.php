<?php

namespace App\Filament\Resources\ObservationResource\Pages;

use App\Enums\ObservationStatusEnum;
use App\Filament\Resources\ObservationResource;
use App\Models\Observation;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewObservation extends ViewRecord
{
    protected static string $resource = ObservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make('Edit')
                ->label('Edit Observation')
                ->form(Observation::getForm())
                ->slideOver(),
            Action::make('review')
                ->label('Send for review')
                ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::DRAFT)
                ->action(fn (Observation $record) => $record->review())
                ->after(function () {
                    Notification::make()
                        ->success()
                        ->title('Observation sent for review')
                        ->body(ObservationStatusEnum::IN_REVIEW->getDescription())
                        ->icon('heroicon-o-paper-airplane')
                        ->send();
                }),
            Action::make('Issue')
                ->label('Issue Audit Observation')
                ->visible(fn (Observation $record) => $record->status === ObservationStatusEnum::IN_REVIEW || $record->status === ObservationStatusEnum::DRAFT)
                ->action(fn (Observation $record) => $record->issue())
                ->after(function () {
                    Notification::make()
                        ->success()
                        ->title('Observation issued to auditee')
                        ->body(ObservationStatusEnum::ISSUED->getDescription())
                        ->icon('heroicon-o-paper-airplane')
                        ->send();
                }),
            // ->icon('heroicon-o-pencil-square')
            // ->route('audits.show', ['audit' => $this->record->audit_id]),
        ];
    }
}
