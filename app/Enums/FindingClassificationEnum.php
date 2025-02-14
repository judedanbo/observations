<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum FindingClassificationEnum: string implements HasColor, HasDescription, HasLabel
{
    case TAX = 'tax';
    case CASH = 'cash';
    case PAYROLL = 'payroll';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TAX => 'Tax Irregularities',
            self::CASH => 'Cash Irregularities',
            self::PAYROLL => 'Payroll Irregularities',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::TAX => 'Findings on tax irregularities',
            self::CASH => 'Findings on cash irregularities',
            self::PAYROLL => 'Findings on payroll irregularities',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TAX => Color::Orange,
            self::CASH => Color::Emerald,
            self::PAYROLL => Color::Rose,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::TAX => 'heroicon-o-document-currency-dollar',
            self::CASH => 'heroicon-o-currency-dollar',
            self::PAYROLL => 'heroicon-o-banknotes',
        };
    }
}
