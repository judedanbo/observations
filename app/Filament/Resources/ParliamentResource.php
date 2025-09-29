<?php

namespace App\Filament\Resources;

use App\Enums\RecommendationStatusEnum;
use App\Filament\Resources\ParliamentResource\Pages;
use App\Models\District;
use App\Models\Institution;
use App\Models\Parliament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParliamentResource extends Resource
{
    protected static ?string $model = Parliament::class;

    protected static ?string $label = 'PAC Directives';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('finding_id')
                    ->relationship('finding', 'title')
                    ->required(),
                Forms\Components\TextInput::make('pac_directive')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('pac_directive_date')
                    ->required(),
                Forms\Components\TextInput::make('client_responsible_officer'),
                Forms\Components\TextInput::make('gas_assigned_officer'),
                Forms\Components\DatePicker::make('implementation_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('PAC Recommendations');
            })
            ->filtersFormColumns(2)
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('finding.title')
                    ->description(fn (Parliament $record): ?string => $record->finding?->observation?->audit?->title)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pac_directive')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pac_directive_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_responsible_officer')

                    ->sortable(),
                Tables\Columns\TextColumn::make('gas_assigned_officer')

                    ->sortable(),
                Tables\Columns\TextColumn::make('implementation_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(RecommendationStatusEnum::class)
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->placeholder('Select Status')
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('observation_id')
                    ->relationship('finding.observation', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select observation')
                    ->label('Observation'),
                Tables\Filters\SelectFilter::make('institution_id')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Institution::all()->pluck('name', 'id'))
                    ->native(false)
                    ->placeholder('Select institution')
                    ->label('Institution')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'finding.observation.audit.institutions',
                        fn (Builder $query) => $query->select('id', 'name')
                    )),
                Tables\Filters\SelectFilter::make('audit_id')
                    ->relationship('finding.observation.audit', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select audit')
                    ->label('Audit')
                    ->query(fn (Builder $query) => $query->whereHas(
                        'finding.observation.audit',
                        fn (Builder $query) => $query->select('id', 'title')
                    )),

                Tables\Filters\SelectFilter::make('region_id')
                    ->searchable()
                    ->preload()
                    ->options(fn () => District::all()->pluck('region.name', 'id'))
                    ->native(false)
                    ->placeholder('Select region')
                    ->label('Region')
                    ->query(function (Builder $query, array $data) {
                        $query->whereHas(
                            'finding.observation',
                            function (Builder $query) use ($data) {
                                $query->whereHas('audit.districts.region', function (Builder $query) use ($data) {
                                    $query->where('regions.id', $data);
                                });
                            }
                        );
                    }),
                Tables\Filters\SelectFilter::make('district_id')
                    ->searchable()
                    ->preload()
                    ->options(fn () => District::all()->pluck('name', 'id'))
                    ->native(false)
                    ->placeholder('Select district')
                    ->label('District')
                    ->query(function (Builder $query, array $data) {
                        $query->whereHas(
                            'finding.observation',
                            function (Builder $query) use ($data) {
                                $query->whereHas('audit.districts', function (Builder $query) use ($data) {
                                    $query->where('districts.id', $data);
                                });
                            }
                        );
                    }),
                // Tables\Filters\SelectFilter::make('department_id')
                //     ->relationship('finding.observation.audit.department', 'short_name')
                //     ->searchable()
                //     ->preload()
                //     ->placeholder('Select department')
                //     ->label('Department')
                //     ->query(fn(Builder $query) => $query->whereHas(
                //         'finding.observation.audit.department',
                //         fn(Builder $query) => $query->select('id', 'short_name')
                //     )),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListParliaments::route('/'),
            // 'create' => Pages\CreateParliament::route('/create'),
            'view' => Pages\ViewParliament::route('/{record}'),
            // 'edit' => Pages\EditParliament::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
