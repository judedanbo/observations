<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum FindingClassificationEnum: string implements HasColor, HasDescription, HasLabel
{
    case TAX = 'tax';
    // case COM = 'compliance';
    // case INT = 'internal_control';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TAX => 'Tax Irregularities',
            // self::COM => 'Compliance',
            // self::INT => 'Internal Control',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::TAX => 'Findings on tax irregularities',
            // self::COM => 'Audit of compliance with laws and regulations',
            // self::INT => 'Audit of internal control systems',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TAX => Color::Red,
            // self::COM => Color::Purple,
            // self::INT => Color::Blue,
        };
    }
}
