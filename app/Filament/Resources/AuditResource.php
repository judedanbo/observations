<?php

namespace App\Filament\Resources;

use App\Enums\AuditStatusEnum;
use App\Filament\Resources\AuditResource\Pages;
use App\Models\Audit;
use App\Models\Observation;
use App\Models\Staff;
use App\Models\Team;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationGroup = 'Audit';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Audit::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Audit title')
                    ->searchable()
                    ->wrap()
                    ->description(fn (Audit $record) => Str::of($record->description)->limit(90)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Audit Status')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('planned_start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_start_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('planned_end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('actual_end_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->visible(auth()->user()?->hasRole(['super-administrator', 'system-administrator']))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->visible(auth()->user()?->hasRole(['super-administrator', 'system-administrator']))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->visible(auth()->user()?->hasRole(['super-administrator', 'system-administrator']))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(AuditStatusEnum::class),
                Filter::make('actual_start_date_from')
                    ->label('Actual start date')
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['actual_start_date_from'] ?? null) {
                            $indicators[] = Indicator::make('Audit start from ' . Carbon::parse($data['actual_start_date_from'])->format('j M Y'))
                                ->removeField('actual_start_date_from');
                        }
                        if ($data['actual_start_date_to'] ?? null) {
                            $indicators[] = Indicator::make('Audit start until ' . Carbon::parse($data['actual_start_date_to'])->format('j M Y'))
                                ->removeField('actual_start_date_to');
                        }
                        return $indicators;
                    })
                    ->form([
                        DatePicker::make('actual_start_date_from')
                            ->closeOnDateSelection()
                            ->weekStartsOnSunday()
                            ->label('Actual date from')
                            ->native(false)
                            ->displayFormat('j M Y'),
                        DatePicker::make('actual_start_date_to')
                            ->closeOnDateSelection()
                            ->weekStartsOnSunday()
                            ->label('Actual start date to')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->searchActualStart($data);
                    }),
                Filter::make('planned_start_date')
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['planned_start_date_from'] ?? null) {
                            $indicators[] = Indicator::make('Planned date start from ' . Carbon::parse($data['planned_start_date_from'])->format('j M Y'))
                                ->removeField('planned_start_date_from');
                        }
                        if ($data['planned_start_date_to'] ?? null) {
                            $indicators = [];
                            $indicators[] = Indicator::make('Planned date until ' . Carbon::parse($data['planned_start_date_to'])->format('j M Y'))
                                ->removeField('planned_start_date_to');
                        }
                        return $indicators;
                    })
                    ->form([
                        DatePicker::make('planned_start_date_from')
                            ->closeOnDateSelection()
                            ->weekStartsOnSunday()
                            ->label('Planned start date from')
                            ->date()
                            ->native(false),
                        DatePicker::make('planned_start_date_to')
                            ->closeOnDateSelection()
                            ->weekStartsOnSunday()
                            ->label('Planned start date to')
                            ->date()
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->searchPlannedStart($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Tables\Actions\Action::make('start')
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
                    Tables\Actions\Action::make('add_team')
                        ->slideOver()
                        ->label('Manage Audit Team')
                        ->icon('heroicon-o-user-plus')
                        ->visible(fn (Audit $record) =>
                        $record->status === AuditStatusEnum::PLANNED
                            || $record->status === AuditStatusEnum::IN_PROGRESS)
                        ->form(
                            [
                                Repeater::make('auditTeams')
                                    ->relationship('auditTeams')
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

                    Tables\Actions\Action::make('observations')
                        ->slideOver()
                        ->form(
                            [
                                Repeater::make('observations')
                                    ->schema(components: Observation::getForm())
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
                    Tables\Actions\Action::make('issue')
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
                    Tables\Actions\Action::make('transmit')
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
                    Tables\Actions\Action::make('Archive')
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

                    Tables\Actions\Action::make('Terminate')
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

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'create' => Pages\CreateAudit::route('/create'),
            // 'edit' => Pages\EditAudit::route('/{record}/edit'),
        ];
    }
}
