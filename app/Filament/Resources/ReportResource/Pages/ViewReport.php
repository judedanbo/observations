<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Parliament;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make('Edit')
                ->form(Report::getForm())
                ->slideOver(),
            ActionGroup::make([
                Action::make('add_parliament_recommendation')
                    // ->relation('parliaments')
                    ->label('Add Parliament Recommendation')
                    ->icon('heroicon-o-play-circle')
                    ->form(Parliament::getForm($this->record->finding_id))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['finding_id'] = $this->record->finding_id;

                        return $data;
                    })
                    ->action(fn (Report $record, array $data) => $record->recommend($data)),
                // ->after(function () {
                //     Notification::make()
                //         ->success()
                //         ->title('Parliament Recommendation added')
                //         ->body('The Parliament Recommendation has been added')
                //         ->send();
                // }),
                // Action::make('Add Parliament Recommendation')
                //     // ->label(fn (Audit $record) => $record->status === AuditStatusEnum::PLANNED ? 'Start audit' : 'Resume audit')
                //     ->icon('heroicon-o-play-circle')
                //     // ->visible(
                //     //     fn (Audit $record) =>
                //     //     $record->status === AuditStatusEnum::PLANNED
                //     //         || $record->status === AuditStatusEnum::TERMINATED
                //     // )
                //     ->form(Parliament::getForm($this->record->finding_id))
                //     ->action(fn (Report $record) => $record->start())
                //     ->slideOver()
                // // ->after(function () {
                // //     Notification::make()
                // //         ->success()
                // //         ->title('Audit started')
                // //         ->body('The audit has been started')
                // //         ->send();
                // // }),
            ]),
        ];
    }
}
