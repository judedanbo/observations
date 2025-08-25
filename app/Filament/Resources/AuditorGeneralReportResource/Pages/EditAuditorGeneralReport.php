<?php

namespace App\Filament\Resources\AuditorGeneralReportResource\Pages;

use App\Filament\Resources\AuditorGeneralReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuditorGeneralReport extends EditRecord
{
    protected static string $resource = AuditorGeneralReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
