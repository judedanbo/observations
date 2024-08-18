<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Enums\AuditDepartmentEnum;
use App\Filament\Resources\ReportResource;
use App\Imports\ObservationImport;
use App\Models\Audit;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All reports'),
            'cgad' => Tab::make('CGAD reports')
                ->modifyQueryUsing(fn (Builder $query) => $query->cgad()),
            'cad_soe' => Tab::make('CAD/SOE reports')
                ->modifyQueryUsing(fn (Builder $query) => $query->cad()),
            'psad' => Tab::make('Performance reports'),
            'is_audit' => Tab::make('IS Audit reports')
                ->modifyQueryUsing(fn (Builder $query) => $query->performance()),
            'special' => Tab::make('Special reports')
                ->modifyQueryUsing(fn (Builder $query) => $query->special()),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-c-document-plus')
                ->slideOver(),
            Actions\Action::make('Load Observations')
                ->icon('heroicon-s-document-arrow-down')
                ->form([
                    Select::make('audit_section')
                        ->enum(AuditDepartmentEnum::class)
                        ->options(AuditDepartmentEnum::class)
                        ->native(false)
                        ->label('Select Audit Type')
                        ->required(),
                    FileUpload::make('filename')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/'.$data['filename']);
                    try {
                        $data = (new ObservationImport($data['audit_section']))->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
                        Notification::make('Observations Loaded')
                            ->title('Observations Loaded')
                            ->body('Observations have been loaded successfully.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make('Observations Load Failed')
                            ->title('Observations Load Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                    // (new ObservationImport($data['audit_section']))->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
                })
                ->after(function () {
                }),

        ];
    }
}
