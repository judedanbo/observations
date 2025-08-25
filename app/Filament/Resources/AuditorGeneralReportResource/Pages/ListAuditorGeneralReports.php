<?php

namespace App\Filament\Resources\AuditorGeneralReportResource\Pages;

use App\Filament\Resources\AuditorGeneralReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuditorGeneralReports extends ListRecords
{
    protected static string $resource = AuditorGeneralReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
