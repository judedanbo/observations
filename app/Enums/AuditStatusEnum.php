<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;

enum AuditStatusEnum: string implements HasLabel, HasDescription, HasColor
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case ISSUED = 'issued';
    case TRANSMITTED = 'transmitted';
    case ARCHIVED = 'archived';
    case TERMINATED = 'terminated';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLANNED => 'Planned',
            self::IN_PROGRESS => 'In Progress',
            self::ISSUED => 'Issued',
            self::TRANSMITTED => 'Transmitted',
            self::ARCHIVED => 'Archived',
            self::TERMINATED => 'Terminated',
        };
    }



    public function getDescription(): string
    {
        return match ($this) {
            self::PLANNED => 'Audit scheduled but not started yet',
            self::IN_PROGRESS => 'Audit in progress',
            self::ISSUED => 'Audit is completed and Management is issued',
            self::TRANSMITTED => 'Audit is report has been submitted to the parliament',
            self::ARCHIVED => 'Completed audits with no further action required',
            self::TERMINATED => 'Audit terminated or cancelled before completion',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PLANNED => Color::Orange,
            self::IN_PROGRESS => Color::Purple,
            self::ISSUED => Color::Blue,
            self::TRANSMITTED => Color::Green,
            self::ARCHIVED => Color::Gray,
            self::TERMINATED => Color::Red,
        };
    }
}
