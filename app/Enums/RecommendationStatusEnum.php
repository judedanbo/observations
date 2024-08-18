<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum RecommendationStatusEnum: string implements HasColor, HasDescription, HasLabel
{
    case Open = 'open';
    case Closed = 'closed';
    // case Overdue = 'overdue';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
            // self::Overdue => 'Overdue',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Open => 'Recommendation is open',
            self::Closed => 'Recommendation is closed',
            // self::Overdue => 'Recommendation is overdue',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => Color::Blue,
            self::Closed => color::Green,
            // self::Overdue => 'red',
        };
    }
}
