<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Enums\AuditDepartmentEnum;
use App\Enums\AuditTypeEnum;
use App\Filament\Resources\ReportResource;
use App\Imports\ObservationImport;
use App\Models\Audit;
use App\Models\Report;
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

    public function getTitle(): string
    {
        return 'ML Observations';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All reports'),
            'mda' => Tab::make('MDA')
                ->modifyQueryUsing(fn(Builder $query) => $query->mda()),
            'national' => Tab::make('National Accounts')
                ->modifyQueryUsing(fn(Builder $query) => $query->national()),
            'dacf' => Tab::make('MMDA DACF')
                ->modifyQueryUsing(fn(Builder $query) => $query->dacf()),
            'igf' => Tab::make('MMDA IGF')
                ->modifyQueryUsing(fn(Builder $query) => $query->igf()),
            'pre' => Tab::make('Pre Tertiary')
                ->modifyQueryUsing(fn(Builder $query) => $query->pre()),
            'seo' => Tab::make('SEO')
                ->modifyQueryUsing(fn(Builder $query) => $query->state()),
            'tertiary' => Tab::make('Tertiary')
                ->modifyQueryUsing(fn(Builder $query) => $query->tertiary()),
            'bog' => Tab::make('BOG')
                ->modifyQueryUsing(fn(Builder $query) => $query->bog()),

            'psad' => Tab::make('Performance Audit')
                ->modifyQueryUsing(fn(Builder $query) => $query->performance()),
            'is_audit' => Tab::make('IS Audit')
                ->modifyQueryUsing(fn(Builder $query) => $query->is()),
            'special' => Tab::make('Special reports')
                ->modifyQueryUsing(fn(Builder $query) => $query->special()),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Download blank template')
                ->icon('heroicon-c-document-arrow-down')
                ->outlined()
                ->url('/Tracking Template - V3.xlsx'),
            Actions\CreateAction::make()
                ->icon('heroicon-c-document-plus')
                ->outlined()
                ->slideOver(),
            Actions\Action::make('Upload ML from Observations')
                ->outlined()
                ->icon('heroicon-s-document-arrow-up')
                ->form([
                    Select::make('audit_section')
                        ->enum(AuditTypeEnum::class)
                        ->options(AuditTypeEnum::class)
                        ->native(false)
                        ->label('Select Audit Type')
                        ->required(),
                    FileUpload::make('filename')
                        ->label('Observations in MS Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required(),
                    FileUpload::make('management_letter')
                        ->label('Audit Management Letter')
                        ->hint('Upload the management letter for the audit in PDF or MS word format')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['filename']);
                    // dd($data['management_letter']);
                    $managementLetter = $data['management_letter'] ? public_path('storage/' . $data['management_letter']) : null;
                    try {
                        $data = (new ObservationImport($data['audit_section'], $managementLetter))
                            ->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
                        // $record->audit()->addManagementLetter($data['management_letter']);
                        Notification::make('Observations Loaded')
                            ->title('Observations Loaded')
                            ->body('Observations have been loaded successfully.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // dd($e);
                        Notification::make('Observations Load Failed')
                            ->title('Observations Load Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                    // (new ObservationImport($data['audit_section']))->import($file, null, \Maatwebsite\Excel\Excel::XLSX);
                })
                ->after(function () {}),

        ];
    }
}
