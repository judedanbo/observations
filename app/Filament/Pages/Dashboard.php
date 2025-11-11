<?php

namespace App\Filament\Pages;

use App\Enums\AuditStatusEnum;
use App\Enums\FindingTypeEnum;
use App\Enums\ObservationStatusEnum;
use App\Models\Department;
use App\Models\District;
use App\Models\Institution;
use App\Models\Unit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends PagesDashboard
{
    use HasFiltersForm;

    public function getColumns(): int|string|array
    {
        return 6;
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filters')
                    ->collapsible(true)
                    ->collapsed()
                  // ->columns(['xl' => 6, 'lg' => 6, 'md' => 6, 'sm' => 1])
                  // ->columnSpanFull()
                  // ->columnSpan([
                  //   'default' => 1,
                  //   'sm' => 2,
                  //   'md' => 3,
                  //   'lg' => 4,
                  //   'xl' => 6,
                  // ])
                    ->schema([
                        // Split::make([
                        //   DatePicker::make('start_date')
                        //     ->native(false)
                        //     ->label('Audit start date'),
                        //   // ->date(),
                        //   DatePicker::make('end_date')
                        //     ->native(false)
                        //     ->label('Audit end date'),
                        // ])
                        //   ->columnSpanFull(),
                        Split::make([
                            Select::make('institutions')
                                ->label('Filter Institution')
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->multiple()
                              // ->columnSpan(2)
                                ->options(Institution::all()->pluck('name', 'id')),
                            Select::make('districts')
                                ->label('Filter district')
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->multiple()
                                ->options(District::all()->pluck('name', 'id')),
                            Select::make('audit_status')
                                ->label('Audit Status')
                                ->enum(AuditStatusEnum::class)
                                ->options(AuditStatusEnum::class)
                                ->native(false)
                                ->label('Audit status'),

                            Select::make('observation_status')
                                ->label('Audit Status')
                                ->enum(ObservationStatusEnum::class)
                                ->options(ObservationStatusEnum::class)
                                ->native(false)
                                ->label('Observation status'),
                            // ->date(),
                            Select::make('finding_type')
                                ->label('Audit Status')
                                ->enum(FindingTypeEnum::class)
                                ->options(FindingTypeEnum::class)
                                ->native(false)
                                ->label('Filter by finding Type'),

                            Select::make('department')
                                ->label('Audit Department')
                                ->options(Department::all()->pluck('short_name', 'id'))
                                ->native(false)
                              // ->multiple()
                                ->label('Filter by department'),
                            Select::make('unit')
                                ->label('Audit Unit')
                                ->searchable()
                                ->options(Unit::all()->pluck('name', 'id'))
                                ->native(false)
                                ->label('Filter by unit'),
                        ]),
                        // ->columnSpanFull(),
                    ]),
            ]);
    }
}
