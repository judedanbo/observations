<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum FindingTypeEnum: string implements HasLabel, HasDescription, HasColor
{
  case FIN = 'financial';
  case COM = 'compliance';
  case INT = 'internal_control';
  public function getLabel(): ?string
  {
    return match ($this) {
      self::FIN => 'Financial',
      self::COM => 'Compliance',
      self::INT => 'Internal Control',
    };
  }
  public function getDescription(): string
  {
    return match ($this) {
      self::FIN => 'Audit of financial statements and financial reporting',
      self::COM => 'Audit of compliance with laws and regulations',
      self::INT => 'Audit of internal control systems',
    };
  }

  public function getColor(): string | array | null
  {
    return match ($this) {
      self::FIN => Color::Orange,
      self::COM => Color::Purple,
      self::INT => Color::Blue,
    };
  }
}
