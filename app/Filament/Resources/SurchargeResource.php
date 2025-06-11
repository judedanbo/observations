<?php

namespace App\Filament\Resources;

use App\Enums\AuditStatusEnum;
use App\Enums\AuditTypeEnum;
use App\Enums\FindingClassificationEnum;
use App\Enums\FindingTypeEnum;
use App\Enums\ObservationStatusEnum;
use App\Filament\Resources\SurchargeResource\Pages;
use App\Models\District;
use App\Models\Office;
use App\Models\Region;
use App\Models\Surcharge;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SurchargeResource extends Resource
{
    protected static ?string $model = Surcharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filters Surcharges');
            })
            ->filtersFormColumns(2)
            ->columns([
                Tables\Columns\TextColumn::make('observation.audit.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('observation.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Finding')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Finding Type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->alignRight()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surcharge_amount')
                    ->numeric()
                    ->alignRight()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(
                [
                    SelectFilter::make('Region')
                        ->native()
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->options(fn() => Region::all()->pluck('name', 'id'))
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['values'], function ($query, $data) {
                                $query->whereHas('observation.audit', function ($query) use ($data) {
                                    $query->whereHas('districts.region', function ($query) use ($data) {
                                        $query->whereIn('regions.id', $data);
                                    });
                                });
                            });
                        }),
                    SelectFilter::make('District')
                        ->native()
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->options(fn() => District::all()->pluck('name', 'id'))
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['values'], function ($query, $data) {
                                $query->whereHas('observation.audit', function ($query) use ($data) {
                                    $query->whereHas('districts', function ($query) use ($data) {
                                        $query->whereIn('districts.id', $data);
                                    });
                                });
                            });
                        }),
                    SelectFilter::make('Audit unit/branch/sector')
                        // ->relationship('audit.units', 'name')
                        ->multiple()
                        ->native()
                        ->searchable()
                        ->preload()
                        ->options(
                            fn() => Unit::all()->pluck('name', 'id')
                        )
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['values'], function ($query, $data) {
                                // dd($data);
                                $query->whereHas('observation.audit', function ($query) use ($data) {
                                    $query->whereHas('units', function ($query) use ($data) {
                                        // $query->whereHas('units', function ($query) use ($data) {
                                        // dd($data);
                                        $query->whereIn('units.id', $data);
                                        // });
                                        // $query->whereIn('offices.id', $data);
                                    });
                                });
                            });
                        }),
                    // SelectFilter::make('Audit office')
                    //     // ->relationship('audit.offices', 'name')
                    //     ->options(
                    //         Office::all()->pluck('name', 'id'),
                    //     )
                    //     ->multiple()
                    //     ->native()
                    //     ->searchable()
                    //     ->preload()
                    //     ->query(function (Builder $query, array $data) {
                    //         $query->when($data['values'], function ($query, $data) {
                    //             // dd($data);
                    //             $query->whereHas('observation.audit.offices', function ($query) use ($data) {
                    //                 // $query->whereHas('offices', function ($query) use ($data) {
                    //                 // $query->whereHas('offices', function ($query) use ($data) {
                    //                 //     // dd($data);
                    //                 $query->whereIn('offices.id', $data);
                    //                 // });
                    //                 // $query->whereIn('offices.id', $data);
                    //             });
                    //             // });
                    //         });
                    //     }),
                    SelectFilter::make('section')
                        ->options(AuditTypeEnum::class)
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->label('Audit section')
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['value'], function ($query, $data) {
                                $query->whereHas('observation.audit', function ($query) use ($data) {
                                    $query->where('type', $data);
                                });
                            });
                        }),
                    SelectFilter::make('Audit status')
                        ->native(false)
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->options(AuditStatusEnum::class)
                        ->label('Audit status')
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['values'], function ($query, $data) {
                                $query->whereHas('observation.audit', function ($query) use ($data) {
                                    $query->where('status', $data);
                                });
                            });
                        }),
                    SelectFilter::make('Audit report title')
                        ->relationship('observation.audit', 'title')
                        ->searchable()
                        ->preload()
                        ->label('Audit report title'),
                    SelectFilter::make('institution_id')
                        ->relationship('observation.audit.institutions', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Institution'),
                    SelectFilter::make('type')
                        ->label('Finding type')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->options(FindingTypeEnum::class)
                        ->query(function (Builder $query, array $data) {
                            $query->when($data['value'], function ($query, $data) {
                                $query->where('type', $data);
                            });
                        }),
                    // SelectFilter::make('finding.observation.status')
                    //     ->label('Observation status')
                    //     ->native(false)
                    //     ->multiple()
                    //     ->searchable()
                    //     ->preload()
                    //     // ->relationship('finding.observation', 'type')
                    //     ->options(ObservationStatusEnum::class)
                    //     ->query(function (Builder $query, array $data) {
                    //         $query->when($data['values'], function ($query, $data) {
                    //             $query->whereHas('finding.observation', function ($query) use ($data) {
                    //                 $query->whereIn('status', $data);
                    //             });
                    //         });
                    //     }),
                    //     SelectFilter::make('classification')
                    //         ->label('Issue classification')
                    //         // ->relationship('finding', 'classification')
                    //         ->options(FindingClassificationEnum::class)
                    //         ->searchable()
                    //         ->preload()
                    //         ->query(function (Builder $query, array $data) {
                    //             $query->when($data['value'], function ($query, $data) {
                    //                 // dd($data);
                    //                 $query->whereHas('finding', function ($query) use ($data) {
                    //                     $query->where('classification', $data);
                    //                 });
                    //             });
                    //         }),
                ],
                // layout: FiltersLayout::AboveContentCollapsible
            )
            ->actions([
                Tables\Actions\Action::make('surcharge')
                    ->form([
                        Forms\Components\TextInput::make('surcharge_amount')
                            ->label('surcharge_amount')
                            ->required(),
                    ])
                    ->action(function ($data, $record) {
                        $record->surcharge([
                            $data['surcharge_amount'],
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSurcharges::route('/'),
            // 'create' => Pages\CreateSurcharge::route('/create'),
            // 'edit' => Pages\EditSurcharge::route('/{record}/edit'),
        ];
    }
}
