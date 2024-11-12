<?php

namespace App\Filament\Exports;

use App\Models\Report;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use PhpParser\Node\Stmt\Label;

class ReportExporter extends Exporter
{
    protected static ?string $model = Report::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('institution.name'),
            ExportColumn::make('audit.title'),
            ExportColumn::make('finding.title'),
            ExportColumn::make('section')
                ->formatStateUsing(function ($state) {
                    return $state->getLabel();
                }),
            ExportColumn::make('paragraphs'),
            ExportColumn::make('title'),
            ExportColumn::make('type')
                ->formatStateUsing(function ($state) {
                    return $state->getLabel();
                }),
            ExportColumn::make('amount'),
            ExportColumn::make('recommendation'),
            ExportColumn::make('amount_recovered'),
            ExportColumn::make('surcharge_amount'),
            ExportColumn::make('implementation_date'),
            ExportColumn::make('implementation_status'),
            ExportColumn::make('comments'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
