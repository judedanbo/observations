<?php

namespace App\Enums;

enum OfficeTypeEnum: string
{
  case HEADQUARTERS = 'headquarters';
  case REGIONAL = 'regional';
  case DISTRICT = 'district';
  case ANNEX = 'annex';

  public function getLabel(): ?string
  {
    return match ($this) {
      self::REGIONAL => 'Regional Office',
      self::DISTRICT => 'District Office',
      self::HEADQUARTERS => 'Headquarters',
      self::ANNEX => 'Headquarters Annex Office',
    };
  }

  public function getDescription(): string
  {
    return match ($this) {
      self::HEADQUARTERS => 'Main office or headquarters',
      self::REGIONAL => 'Office located at the regional level',
      self::DISTRICT => 'Office located at the district level',
      self::ANNEX => 'Annex office of the headquarters',
    };
  }

  public function getColor(): string | array | null
  {
    return match ($this) {
      self::HEADQUARTERS => 'blue',
      self::REGIONAL => 'green',
      self::DISTRICT => 'yellow',
      self::ANNEX => 'orange',
    };
  }
  public function getIcon(): string
  {
    return match ($this) {
      self::HEADQUARTERS => 'heroicon-o-building-office',
      self::REGIONAL => 'heroicon-o-globe-alt',
      self::DISTRICT => 'heroicon-o-map',
      self::ANNEX => 'heroicon-o-archive',
    };
  }
  public function getValue(): string
  {
    return $this->value;
  }
}
