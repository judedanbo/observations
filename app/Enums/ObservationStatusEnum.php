<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum ObservationStatusEnum: string implements HasColor, HasDescription, HasLabel
{
    case DRAFT = 'draft';
    case IN_REVIEW = 'in_review';
    case ISSUED = 'issued';
    case RECEIVED = 'received';
    case RESPONDED = 'responded';
    case TEAM_RESOLVED = 'team_resolved';
    case REPORTED = 'reported';
    case DA_RESOLVED = 'da_resolved';
    case RA_RESOLVED = 'ra_resolved';
    case AG_RESOLVED = 'ag_resolved';
    case PAC_RESOLVED = 'pac_resolved';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::IN_REVIEW => 'In Review',
            self::ISSUED => 'Issued',
            self::RECEIVED => 'Received',
            self::RESPONDED => 'Responded',
            self::TEAM_RESOLVED => 'Team Resolved',
            self::REPORTED => 'Reported',
            self::DA_RESOLVED => 'DA/ Resolved',
            self::RA_RESOLVED => 'RA/Sector Resolved',
            self::AG_RESOLVED => 'DAG Resolved',
            self::PAC_RESOLVED => 'PAC Resolved',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Audit Observation has been prepared for review',
            self::IN_REVIEW => 'Audit observation is been submitted for review before issue',
            self::ISSUED => 'Audit observation has been issued to the Management of Auditee',
            self::RECEIVED => 'Audit Observation has been received by management of Auditee',
            self::RESPONDED => 'Management of Auditee has responded to observation',
            self::TEAM_RESOLVED => 'Audit Observation has been resolved with the audit team',
            self::REPORTED => 'Audit Observation has been included in the Management letter',
            self::DA_RESOLVED => 'Audit Observation has been resolved with the District Auditor /Branch head or equivalent ',
            self::RA_RESOLVED => 'Audit Observation has been resolved with the Regional Auditor  or any one AAG or below',
            self::RA_RESOLVED => 'Audit Observation has been resolved with the DAG or any one AG',
            self::PAC_RESOLVED => 'Audit Observation has been resolved with the Parliamentary Account Committee or Parliament',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => Color::Orange,
            self::IN_REVIEW => Color::Purple,
            self::ISSUED => Color::Cyan,
            self::RECEIVED => Color::Green,
            self::RESPONDED => Color::Blue,
            self::TEAM_RESOLVED => Color::Gray,
            self::REPORTED => Color::Red,
        };
    }
}
