<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum RecommendationStatusEnum: string implements HasColor, HasDescription, HasLabel
{
    case OPEN = 'open';
    case CLOSE = 'closed';
    // case Overdue = 'overdue';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSE => 'Closed',
            // self::Overdue => 'Overdue',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::OPEN => 'Recommendation is open',
            self::CLOSE => 'Recommendation is closed',
            // self::Overdue => 'Recommendation is overdue',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPEN => Color::Blue,
            self::CLOSE => color::Green,
            // self::Overdue => 'red',
        };
    }
}
