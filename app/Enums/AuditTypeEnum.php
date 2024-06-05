<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Colors\Color;

enum AuditTypeEnum: string implements HasLabel, HasDescription, HasColor
{
  case MDA = 'mda';
  case NATIONAL = 'national_accounts';
  case DACF = 'mmda_dacf';
  case IGF = 'mmda_igf';
  case PRE = 'pre_tertiary';
  case SEO = 'seo';
  case TERTIARY = 'tertiary_institutions';
  case BOG = 'bog_foreign_receipts';
  case PERFORMANCE = 'performance';
  case SPECIAL = 'special';
  case IS = 'information_systems';

  public function getLabel(): string
  {
    return match ($this) {
      self::MDA => "Ministry Department and Agencies",
      self::NATIONAL => "National Accounts",
      self::DACF => "MMDAs District Assembly Common Fund",
      self::IGF => "MMDAs Internally Generated Fund",
      self::PRE => "Pre Tertiary Educational Institutions",
      self::SEO => "Statutory Boards and Corporations",
      self::TERTIARY => "Tertiary Educational Institutions",
      self::BOG => "Foreign Receipts and Payments Bank of Ghana",
      self::PERFORMANCE => "Performance Audit",
      self::SPECIAL => "Special Audit",
      self::IS => "Information Systems Audit",
    };
  }

  public function getDescription(): string
  {
    return match ($this) {
      self::MDA => "Ministry Department and Agencies",
      self::NATIONAL => "National Accounts",
      self::DACF => "MMDA District Assembly Common Fund",
      self::IGF => "MMDA Internally Generated Fund",
      self::PRE => "Pre Tertiary Educational Institutions",
      self::SEO => "Statutory Boards and Corporations",
      self::TERTIARY => "Tertiary Educational Institutions",
      self::BOG => "Foreign Receipts and Payments Bank of Ghana",
      self::PERFORMANCE => "Performance Audit",
      self::SPECIAL => "Special Audit",
      self::IS => "Information Systems Audit",
    };
  }

  public function getColor(): string | array | null
  {
    return match ($this) {
      self::MDA => Color::Blue,
      self::NATIONAL => Color::Green,
      self::DACF => Color::Yellow,
      self::IGF => Color::Rose,
      self::PRE => Color::Lime,
      self::SEO => Color::Cyan,
      self::TERTIARY => Color::Fuchsia,
      self::BOG => Color::Red,
      self::PERFORMANCE => Color::Orange,
      self::SPECIAL => Color::Amber,
      self::IS => Color::Pink,
    };
  }
}
