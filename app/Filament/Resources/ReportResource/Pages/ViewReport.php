<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Document;
use App\Models\Parliament;
use App\Models\Report;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
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
                    ->action(fn(Report $record, array $data) => $record->recommend($data)),
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
                Action::make('add_document')
                    ->label('Add supporting Document')
                    ->icon('heroicon-o-document-plus')
                    ->form(Document::getForm())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['report_id'] = $this->record->id;

                        return $data;
                    })
                    ->action(fn(Report $record, array $data) => $record->addDocuments($data)),

                Action::make('Surcharge')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        TextInput::make('surcharge_amount')
                            ->label('Surcharge Amount')
                            ->placeholder('Enter Surcharge Amount')
                            ->required(),
                    ])
                    ->action(function ($data, $record) {
                        $newAmount = $record->finding()->update([
                            'surcharge_amount' => $data['surcharge_amount']
                        ]);
                        // dd($record->finding);
                        // $record->save();
                        Notification::make('Surcharge Added')
                            ->title('Surcharge Added')
                            ->body('The surcharge has been added successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('Record recovery')
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
                        $finding = $record->finding->recoveries()->create($data);
                    }),
            ]),
        ];
    }
}
