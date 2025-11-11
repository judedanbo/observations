<?php

namespace App\Filament\Resources\AuditorGeneralReportResource\Pages;

use App\Filament\Resources\AuditorGeneralReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditorGeneralReport extends ViewRecord
{
    protected static string $resource = AuditorGeneralReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manage_findings')
                ->label('Manage Findings')
                ->icon('heroicon-o-document-plus')
                ->url(fn () => AuditorGeneralReportResource::getUrl('manage-findings', ['record' => $this->record]))
                ->visible(fn () => $this->record->canBeEdited()),

            Actions\EditAction::make()
                ->visible(fn () => $this->record->canBeEdited()),
        ];
    }
}
