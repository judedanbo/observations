<?php

namespace App\Filament\Pages;

use App\Enums\AuditDepartmentEnum;
use App\Enums\AuditStatusEnum;
use App\Enums\FindingTypeEnum;
use App\Enums\ObservationStatusEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends PagesDashboard
{
  // use HasFiltersForm;

  public function getColumns(): int|string|array
  {
    return [
      'lg' => 3,
      'xl' => 6,
    ];
  }

  // public function filtersForm(Form $form): Form
  // {
  //   return $form
  //     ->schema([
  //       Section::make('Filters')
  //         ->collapsible(true)
  //         ->collapsed()
  //         ->columns(['xl' => 5])
  //         ->schema([
  //           Split::make([
  //             DatePicker::make('start_date')
  //               ->native(false)
  //               ->label('Audit start date'),
  //             // ->date(),
  //             DatePicker::make('end_date')
  //               ->native(false)
  //               ->label('Audit end date'),
  //           ])
  //             ->columnSpanFull(),
  //           Split::make([
  //             Select::make('audit_status')
  //               ->label('Audit Status')
  //               ->enum(AuditStatusEnum::class)
  //               ->options(AuditStatusEnum::class)
  //               ->native(false)
  //               ->label('Audit status'),

  //             Select::make('observation_status')
  //               ->label('Audit Status')
  //               ->enum(ObservationStatusEnum::class)
  //               ->options(ObservationStatusEnum::class)
  //               ->native(false)
  //               ->label('Observation status'),
  //             // ->date(),
  //             Select::make('finding_type')
  //               ->label('Audit Status')
  //               ->enum(FindingTypeEnum::class)
  //               ->options(FindingTypeEnum::class)
  //               ->native(false)
  //               ->label('Filter by finding Type'),

  //             Select::make('unit_department')
  //               ->label('Audit Status')
  //               ->enum(AuditDepartmentEnum::class)
  //               ->options(AuditDepartmentEnum::class)
  //               ->native(false)
  //               // ->multiple()
  //               ->label('Filter by department/unit'),
  //             // ->date(),
  //           ])
  //             ->columnSpanFull(),
  //         ]),
  //     ]);
  // }
}
