<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AuditDepartmentEnum: string implements HasLabel, HasDescription, HasColor
{
  case CGAD = 'central_government';
  case CAD_SOE = 'commercial__soe_audit';
  case PSAD = 'performance_audit';
  case IS_AUDIT = 'information_system';
  case SPECIAL = 'special_audit';

  public function getLabel(): ?string
  {
    return match ($this) {
      self::CGAD => 'Central Government Audit',
      self::CAD_SOE => 'Commercial and SOE Audit',
      self::PSAD => 'Performance Audit',
      self::IS_AUDIT => 'Information System',
      self::SPECIAL => 'Special Audit',
    };
  }
  public function getDescription(): string
  {
    return match ($this) {
      self::CGAD => 'Central Government Audit - Audit of financial statements and financial reporting',
      self::CAD_SOE => 'Commercial and State Owned Enterprise Audit - Audit',
      self::PSAD => 'Performance Audit - Audit of compliance with laws and regulations',
      self::IS_AUDIT => 'Information System - Audit of internal control systems',
      self::SPECIAL => 'Special Audits',
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
