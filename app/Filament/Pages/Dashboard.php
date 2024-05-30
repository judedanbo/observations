<?php

namespace App\Filament\Pages;

use App\Enums\AuditDepartmentEnum;
use App\Enums\AuditStatusEnum;
use App\Enums\FindingTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends PagesDashboard
{
  use HasFiltersForm;

  function filtersForm(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('')
          ->columns(['xl' => 5])
          ->schema([
            DatePicker::make('start_date')
              ->native(false)
              ->label('Audit start date'),
            // ->date(),
            DatePicker::make('end_date')
              ->native(false)
              ->label('Audit end date'),

            Select::make('audit_stutus')
              ->label('Audit Status')
              ->enum(FindingTypeEnum::class)
              ->options(FindingTypeEnum::class)
              ->native(false)
              ->label('Filter by audit status'),
            // ->date(),
            Select::make('Finding Type')
              ->label('Audit Status')
              ->enum(AuditStatusEnum::class)
              ->options(AuditStatusEnum::class)
              ->native(false)
              ->label('Filter by finding Type'),

            Select::make('unit_department')
              ->label('Audit Status')
              ->enum(AuditDepartmentEnum::class)
              ->options(AuditDepartmentEnum::class)
              ->native(false)
              ->label('Filter by department/unit'),
            // ->date(),
          ])
      ]);
  }
}
