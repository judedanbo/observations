<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Enums\AuditStatusEnum;
use App\Filament\Resources\AuditResource;
use App\Models\Audit;
use App\Models\Observation;
use App\Models\Staff;
use App\Models\Team;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make('Edit')
                ->form(Audit::getForm())
                ->slideOver(),
            ActionGroup::make([
                Action::make('start')
                    ->label(fn (Audit $record) => $record->status === AuditStatusEnum::PLANNED ? 'Start audit' : 'Resume audit')
                    ->icon('heroicon-o-play-circle')
                    ->visible(
                        fn (Audit $record) =>
                        $record->status === AuditStatusEnum::PLANNED
                            || $record->status === AuditStatusEnum::TERMINATED
                    )
                    ->action(fn (Audit $record) => $record->start())
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit started')
                            ->body('The audit has been started')
                            ->send();
                    }),
                // TODO add support for multiple teams with relationships 
                Action::make('audit_team')
                    ->slideOver()
                    ->label('Manage Audit Team')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (Audit $record) =>
                    $record->status === AuditStatusEnum::PLANNED
                        || $record->status === AuditStatusEnum::IN_PROGRESS)
                    ->form(
                        [
                            Repeater::make('audit_teams')
                                ->schema([
                                    Select::make('team_id')
                                        ->live(onBlur: true)
                                        ->relationship(
                                            'teams',
                                            'name',
                                        )
                                        ->editOptionForm(Team::getForm())
                                        ->createOptionForm(Team::getForm())
                                        ->preload()
                                        ->default(fn (Audit|null $record) => $record?->teams()->first()?->id ?? null)
                                        ->searchable()
                                        ->label('Team')
                                        ->required(),

                                    Select::make('staff')
                                        ->options(Staff::pluck('name', 'id')->toArray())
                                        ->searchable()

                                        ->multiple()
                                        ->label('Staff')
                                        // ->default(function (Audit $record, Get $get) {
                                        //     return $record->teams()->where('id', $get('team_id'))->first()?->staff()->pluck('id')->toArray();
                                        // })
                                        ->createOptionForm(Staff::getForm())
                                        ->createOptionUsing(function ($data) {
                                            return Staff::create($data)->id;
                                        })

                                ])
                                ->reorderableWithButtons()
                                ->collapsible()
                            // ->itemLabel(fn (array $state): ?string => $state['team_id'] ?? null)
                        ]
                    )
                    ->action(function (Audit $record, array $data) {
                        // dd($data);
                        collect($data['audit_team'])->each(function ($team) use ($record) {
                            $record->addTeamMember(team: $team['team_id'], member: $team['staff']);
                        });
                    })
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit team member added')
                            ->body('New Audit team members have been added to the audit team')
                            ->send();
                    }),

                Action::make('observations')
                    ->slideOver()
                    ->form(
                        [
                            Repeater::make('observations')
                                ->schema(components: Observation::getForm())
                                ->collapsible()
                                ->reorderableWithButtons()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ]
                    )
                    ->label('Create Observations')
                    ->icon('heroicon-o-document-text')
                    ->visible(
                        fn (Audit $record) =>
                        $record->status === AuditStatusEnum::IN_PROGRESS
                    )
                    ->action(function (array $data) {
                        collect($data['observations'])->each(function ($observation) {
                            // $observation->addTeamMember($team: null, $member: );
                        });
                        $audit = Audit::find($data['id']);
                        $audit->observations()->createMany($data['observations']);
                    }),
                Action::make('issue')
                    ->label('Issue audit report')
                    ->icon('heroicon-o-document-duplicate')
                    ->visible(
                        fn (Audit $record) =>
                        $record->status === AuditStatusEnum::IN_PROGRESS
                    )
                    ->action(fn (Audit $record) => $record->issue())
                    ->requiresConfirmation()
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit report issued')
                            ->body('The audit report has been started')
                            ->send();
                    }),
                Action::make('transmit')
                    ->label('Initiate transmission')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(
                        fn (Audit $record) =>
                        $record->status === AuditStatusEnum::ISSUED
                    )
                    ->action(fn (Audit $record) => $record->transmit())
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit report is ready to be transmitted')
                            ->body('The audit report has been prepared for transmission')
                            ->send();
                    }),
                Action::make('Archive')
                    ->label('Archive audit')
                    ->icon('heroicon-o-archive-box')
                    ->color(Color::Gray)
                    ->visible(fn (Audit $record) => $record->status === AuditStatusEnum::ISSUED || $record->status === AuditStatusEnum::TRANSMITTED)
                    ->action(fn (Audit $record) => $record->archive())
                    ->requiresConfirmation()
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit terminated')
                            ->body('The audit has been ended')
                            ->send();
                    }),

                Action::make('Terminate')
                    ->label('Terminate audit')
                    ->icon('heroicon-o-x-circle')
                    ->color(Color::Red)
                    ->visible(fn (Audit $record) => $record->status === AuditStatusEnum::PLANNED || $record->status === AuditStatusEnum::IN_PROGRESS)
                    ->action(fn (Audit $record) => $record->terminate())
                    ->requiresConfirmation()
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Audit terminated')
                            ->body('The audit has been ended')
                            ->send();
                    })
            ]),
        ];
    }
}
