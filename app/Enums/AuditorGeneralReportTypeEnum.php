<?php

namespace App\Enums;

enum AuditorGeneralReportTypeEnum: string
{
    case ANNUAL = 'annual';
    case QUARTERLY = 'quarterly';
    case SPECIAL = 'special';
    case PERFORMANCE = 'performance';
    case THEMATIC = 'thematic';

    public function getLabel(): string
    {
        return match ($this) {
            self::ANNUAL => 'Annual Report',
            self::QUARTERLY => 'Quarterly Report',
            self::SPECIAL => 'Special Investigation Report',
            self::PERFORMANCE => 'Performance Audit Report',
            self::THEMATIC => 'Thematic Report',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::ANNUAL => 'Comprehensive annual report covering all audit activities',
            self::QUARTERLY => 'Quarterly summary of audit findings and activities',
            self::SPECIAL => 'Special investigation or urgent audit matters',
            self::PERFORMANCE => 'Performance and value-for-money audit findings',
            self::THEMATIC => 'Focused report on specific themes or sectors',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
