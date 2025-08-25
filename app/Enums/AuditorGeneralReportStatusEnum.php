<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AuditorGeneralReportStatusEnum: string implements HasColor, HasLabel, HasDescription
{
    case DRAFT = 'draft';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case PUBLISHED = 'published';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::PUBLISHED => 'Published',
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::DRAFT => Color::Gray,
            self::UNDER_REVIEW => Color::Amber,
            self::APPROVED => Color::Green,
            self::PUBLISHED => Color::Blue,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document',
            self::UNDER_REVIEW => 'heroicon-o-clock',
            self::APPROVED => 'heroicon-o-check-circle',
            self::PUBLISHED => 'heroicon-o-globe-alt',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Report is being prepared and can be edited',
            self::UNDER_REVIEW => 'Report is under review by supervisors',
            self::APPROVED => 'Report has been approved and ready for publication',
            self::PUBLISHED => 'Report has been officially published',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::DRAFT => in_array($status, [self::UNDER_REVIEW]),
            self::UNDER_REVIEW => in_array($status, [self::DRAFT, self::APPROVED]),
            self::APPROVED => in_array($status, [self::UNDER_REVIEW, self::PUBLISHED]),
            self::PUBLISHED => false, // Published reports cannot be changed
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::UNDER_REVIEW]);
    }
}
